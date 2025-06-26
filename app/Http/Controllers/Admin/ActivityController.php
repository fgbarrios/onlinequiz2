<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityMultiple;
use App\Models\ActivitySettings;
use App\Models\ActivityOption;
use App\Models\ActivityResponse;
use App\Models\Visitors;
use App\Models\User;
use App\Models\GeneralSettings;
use App\Mail\CustomSendMail;
use Auth;
use Hash;
use Validator;
use DB;
Use Mail;
use Session;
use Cache;
use Illuminate\Support\Facades\Response;
use League\Csv\Writer;
use Twilio\Rest\Client as TwilioClient;
use Twilio\TwiML\MessagingResponse;
use WebSocket\Client;

class ActivityController extends Controller
{
    # List activity page
    public function list_activity(Request $request)
    {
        $activity_data      = Activity::orderBy('sort_order','asc')->get();
        $totalResponse      = Activity::select(DB::raw('IFNULL(SUM(total_responses),0) as total'))->get();
        $total_response     = 0;
        if(count($totalResponse)>0)
        {
            $total_response = $totalResponse[0]->total;
        }
        return view('admin.activity.list_activity',compact('activity_data','total_response'));
    }
    
    # List activity page
    public function load_activity_data_sort(Request $request)
    {
        $from           = $request->from;
        $limit          = $request->limit;
        $search         = $request->search;

        $sql    = Activity::where('sort_order','>',0);
        if($search !="")
        {
            $sql = $sql->where("title", "LIKE","%".$search."%");
        }
        // $activity_data  = $sql->orderBy('sort_order','asc')->offset($from)->limit($limit)->get();
        $activity_data  = $sql->orderBy('sort_order','asc')->get();
        $data           = array();

        foreach ($activity_data as $key => $value) 
        {
            if($value->is_multiple_type=="2")
            {
                $datas = ActivityOption::select(DB::raw('SUM(select_count) as total'))->where('activity_id',$value->id)->where('select_count','>',0)->get();
                if(count($datas)>0)
                {
                    $total = $datas[0]->total;
                }
                else
                {
                    $total = 0;
                }
            }
            else
            {
                $total = $value->total_responses;
            }
            
            $temp = array();
            $temp['id']                 = $value->id;
            $temp['encode']             = app_encode($value->id);
            $temp['sort_order']         = $value->sort_order;
            $temp['title']              = $value->title;
            $temp['total_responses']    = $total;
            $temp['status']             = $value->status;
            $temp['last_change']        = app_date_format($value->last_change);
            $data[]                     = $temp;
        }
        return response()->json(["data"=> $data],200); 
    }

    # Add activity page
    public function add_activity(Request $request)
    {
        return view('admin.activity.activity_form');
    }

    # Save activity data
    public function save_activity(Request $request)
    {
        // dd($request);
        // exit();
        $validator = Validator::make($request->all(), [

            'title_type'        => 'required',
            'title'             => '',
            'is_had_score'      => 'required',
            'is_multiple_type'  => 'required',
            'title_file'        => 'mimes:png,jpg,jpeg,webp|max:2048'

        ]);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors());
        }
        try
        {
            $activity_id            = app_decode($request->activity_id);
            $title_type             = $request->title_type;
            $title                  = $request->title;
            $is_had_score           = $request->is_had_score;
            $is_multiple_type       = $request->is_multiple_type;
            $total_options          = $request->total_options;
            $submit_type            = $request->submit_type;
            $is_delete_title_image  = $request->is_delete_title_image;

            if($title == "")
            {
                return redirect()->back()->withErrors(array(trans('admin.activity_title_error')));
            }

            $sort_order = 1;
            $last_data  = Activity::select('sort_order')->orderBy('sort_order','desc')->get();
            if(count($last_data)>0)
            {
                $sort_order = $last_data[0]->sort_order;
                $sort_order++;
            }

            $save_data = array(
                "title_type"        => $title_type,
                "title"             => $title,
                "sort_order"        => $sort_order,
                "is_had_score"      => $is_had_score,
                "is_multiple_type"  => $is_multiple_type,
                "created_by"        => Auth::user()->id,
                "ip_address"        => $request->ip(),
            );

            $old_title_image    = "";
            $old_data = Activity::select('title_image')->where('id',$activity_id)->get();
            if(count($old_data)>0)
            {
                $old_title_image = $old_data[0]->title_image;
            }

            $title_image    = "";
            if ($request->hasFile('title_file') && $request->file('title_file')->isValid()) 
            {
                if($old_title_image !="")
                {
                    if(file_exists($old_title_image))
                    {
                        unlink($old_title_image);
                    }
                }
                $imagePath      = $request->file('title_file')->store('uploads/activity/title', 'public');
                $title_image    = 'storage/' . $imagePath;
            }

            if($title_image !="")
            {
                $save_data['title_image'] = $title_image;
            }

            if($is_delete_title_image==1 || $is_delete_title_image=="1")
            {
                $save_data['title_image'] = "";
            }

            $save_data['last_change'] = date('Y-m-d H:i:s');

            $is_option_name  = 0;
            if($activity_id == "")
            {
                $activity_id    = Activity::insertGetId($save_data);
                $message        = trans('admin.activity_saved_successfully');
            }
            else
            {
                unset($save_data['sort_order']);
                if($request->is_multiple_type=="2")
                {
                    $validator = Validator::make($request->all(), [
                        'response_type'     => 'required',
                        'may_respond'       => 'required',
                        'may_select'        => 'required',
                    ]);
                    if($validator->fails())
                    {
                        return redirect()->back()->withErrors($validator->errors());
                    }
                }
                else
                {
                    $validator = Validator::make($request->all(), [
                        'response_type'     => 'required',
                    ]);
                    if($validator->fails())
                    {
                        return redirect()->back()->withErrors($validator->errors());
                    }
                }
                
                $save_data['response_type']         = implode(',',$request->response_type);
                $save_data['option_text_type']      = "";
                if(in_array('2', $request->response_type))
                {
                    $save_data['option_text_type'] = $request->option_text_type;
                    $is_option_name++;
                }
                $save_data['may_respond']           = $request->may_respond;
                $save_data['may_select']            = $request->may_select;
               
                $save_data['may_respond_count']     = config('constant.unlimited_value');
                if($request->may_respond == "1")
                {
                    $save_data['may_respond_count'] = $request->may_respond_count;
                }

                $save_data['may_select_count']     = config('constant.unlimited_value');
                if($request->may_select == "1")
                {
                    $save_data['may_select_count'] = $request->may_select_count;
                }
                Activity::where('id',$activity_id)->update($save_data);
                $message = trans('admin.activity_updated_successfully');
            }
            
            if($activity_id)
            {
                ActivityMultiple::where('activity_id',$activity_id)->forceDelete();
                ActivityOption::where('activity_id',$activity_id)->forceDelete();
                ActivityResponse::where('activity_id',$activity_id)->forceDelete();
                if($is_multiple_type == 1)
                {
                    for ($i=0; $i < $total_options; $i++) 
                    {
                        if($request->has('question_'.$i))
                        {
                            $option_id          = $request->{'option_id_'.$i};
                            $question           = $request->{'question_'.$i};
                            $sort_order         = (int)$request->{'sord_order_'.$i};

                            $multi_option_data  = array(

                                "activity_id"   => $activity_id,
                                "question"      => $question,
                                "sort_order"    => $sort_order,

                            );
                            $sub_activity_id = ActivityMultiple::insertGetId($multi_option_data);
                            if($sub_activity_id)
                            {
                                $option_length = 2;
                                for ($j=1; $j <= $option_length; $j++) 
                                { 
                                    $score           = $request->{'score_'.$i.'_'.$j};
                                    $option          = $request->{'option_'.$i.'_'.$j};
                                    $sort_order      = $j;

                                    $option_data = array(

                                        "activity_id"       => $activity_id,
                                        "sub_activity_id"   => $sub_activity_id,
                                        "sort_order"        => $sort_order,
                                        "is_correct"        => 2,
                                        "score"             => $score,
                                        "option"            => $option,
                                        "answer_type"       => 1,

                                    );

                                    ActivityOption::create($option_data);
                                }
                            }
                        }
                    }
                }
                else if($is_multiple_type == 2)
                {
                    for ($i=0; $i < $total_options; $i++) 
                    { 
                        if($request->has('is_correct_'.$i))
                        {
                            $option_id          = $request->{'option_id_'.$i};
                            $is_correct         = $request->{'is_correct_'.$i};
                            $score              = $request->{'score_'.$i};
                            $option_text        = $request->{'option_text_'.$i};
                            $option_file        = $request->{'option_file_url_'.$i};
                            $option_name        = $request->{'option_name_value_'.$i};
                            $sort_order         = (int)$request->{'sord_order_'.$i};
                            $answer_type        = "1";
                            $select_count       = 0;
                            $name               = "";

                            if($is_option_name==0)
                            {
                                $option_name = "";
                            }

                            if($is_had_score !="1" || $is_had_score !=1)
                            {
                                $score = 0;
                            }
                            if($option_file)
                            {
                                $explode = explode('/', $option_file);
                                if(isset($explode[5]))
                                {
                                    $filename   = $explode[5];
                                    if($explode[4] =="temp")
                                    {
                                        $name       = 'storage/uploads/activity/option/'.$filename;
                                        File::move(public_path($option_file),public_path($name));
                                    }
                                }
                                if($explode[4] !="temp")
                                {
                                    $name = $option_file;
                                }
                            }
                            if($option_file)
                            {
                                $answer_type = "2";
                            }
                            $option_image_name = "";
                            if($name)
                            {
                                $option_image_name = $name;
                            }

                            $option_array   = array(

                                "activity_id"       => $activity_id,
                                "is_correct"        => $is_correct,
                                "score"             => $score,
                                "sort_order"        => $sort_order,
                                "option"            => $option_text,
                                "option_image"      => $option_image_name,
                                "answer_type"       => $answer_type,
                                "select_count"      => $select_count,
                                "option_name"       => $option_name,
                                "ip_address"        => $request->ip(),

                            );
                            if($option_text !="" || $option_file !="")
                            {
                                ActivityOption::create($option_array);
                            }
                        }
                    }
                }
            }
            if($submit_type=="1" || $submit_type ==1)
            {
                return redirect()->route('list-activity')->with('success',$message);
            }
            else
            {
                return redirect()->back()->with('success',$message);
            }
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    # Edit activity page
    public function edit_activity($id)
    {
        $activity_id        = app_decode($id);
        $edit_data          = Activity::where('id',$activity_id)->get();
        if(count($edit_data)==0)
        {
            return redirect()->route('list-activity');
        }
        if($edit_data[0]->total_responses>0)
        {
            return redirect()->back();
        }
        $options = array();
        if($edit_data[0]->is_multiple_type=="1")
        {
            $SubActivityData = ActivityMultiple::where('activity_id',$activity_id)->orderBy('sort_order','asc')->get();
            foreach ($SubActivityData as $value12) 
            {
                $temp                   = array();
                $temp['row']            = $value12->sort_order;
                $temp['question']       = $value12->question;
                $temp['sort_order']     = $value12->sort_order;
                $optionsArray           = array();
                $SubActivityData        = ActivityOption::where('activity_id',$value12->activity_id)->where('sub_activity_id',$value12->id)->orderBy('sort_order','asc')->get();
                foreach ($SubActivityData as $value13) 
                {
                    $tempArray                  = array();
                    $tempArray['option_id']     = $value13->id;
                    $tempArray['option_name']   = "";
                    $tempArray['options']       = $value13->option;
                    $tempArray['options_no']    = $value13->sort_order;
                    $tempArray['score']         = $value13->score;
                    $optionsArray[]             = $tempArray;
                }
                $temp['options_array']          = $optionsArray;
                $options[]                      = $temp;
            }
        }
        else
        {
            $edit_data_options  = ActivityOption::where('activity_id',$activity_id)->orderBy('sort_order','asc')->get();
            foreach ($edit_data_options as $key => $value) 
            {
                $temp                   = array();
                $temp['row']            = $value->sort_order;
                $temp['score']          = $value->score;
                $temp['option_text']    = $value->option;
                $temp['is_correct']     = $value->is_correct;
                $temp['option_id']      = $value->id;
                $temp['sort_order']     = $value->sort_order;
                $temp['option_name']    = $value->option_name;
                $temp['image_url']      = "";
                if($value->option_image)
                {
                    $temp['image_url']      = $value->option_image;
                }
                $options[] = $temp;
            }
        }
        $edit_data_options  = $options;
        $sms_number         = "";
        $general_data       = GeneralSettings::all();
        if(count($general_data)>0)
        {
            $from_code      = $general_data[0]->twilio_from_code;
            $from_number    = $general_data[0]->twilio_from_number;
            $sms_number     = '+'.$from_code.$from_number;
        }

        $activity_data          = ActivitySettings::select('is_text_message')->first();
        return view('admin.activity.activity_form',compact('edit_data','edit_data_options','id','activity_data','sms_number'));
    }

    # Upload option image
    public function upload_option_image(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'option_image'    => 'required|max:2048'

        ]);
        if($validator->fails())
        {
            return response()->json(["message"=> $validator->errors()->all()[0]],400);
        }

        try {
            
            $option_image_url   = "";
            if ($request->hasFile('option_image') && $request->file('option_image')->isValid()) 
            {
                $imagePath      = $request->file('option_image')->store('uploads/activity/option/temp', 'public');
                $option_image_url    = 'storage/' . $imagePath;
            }
            return response()->json(["url"=> $option_image_url],200);

        } catch (Exception $e) {
            return response()->json(["message"=> $e->getMessage()],400);
        }
    }

    # Change status of the activity
    public function change_activity_status(Request $request)
    {
        $status         = $request->status;
        $activity_id    = $request->ref;
        Activity::where('id','>','0')->update(array("status" => "2"));
        
        if($status == 1 || $status == '1')
        {
            $update_array = array("activated_at" => date('Y-m-d H:i:s'));
        }
        else if($status == 2 || $status == '2')
        {
            $update_array = array("deactivated_at" => date('Y-m-d H:i:s'));
        }

        $update_array['status'] = $status;
        Activity::where('id',$activity_id)->update($update_array);
        return response()->json(["code"=> "200","status" => $status],200);
    }

    # Update activities sorting order
    public function update_activity_sort_order(Request $request)
    {
        $data = $request->data;
        foreach ($data as $value) 
        {
            $id     = app_decode($value['ref']);
            $order  = $value['order'];

            $update_data = array(
                "sort_order" => $order,
            );
            Activity::where('id',$id)->update($update_data);
        }
        return response()->json(["code"=> "200","data" => $data],200);
    }

    # Delete Activity
    public function delete_activity(Request $request)
    {
        $id     = app_decode($request->ref);
        Activity::where('id',$id)->update(['status' => '2']);
        Activity::where('id',$id)->delete();
        return response()->json(["code"=> "200"],200);
    }

    # Clone Activity
    public function clone_activity(Request $request)
    {
        $id                     = app_decode($request->ref);
        $data                   = Activity::where('id',$id)->first();
        $last_data  = Activity::select('sort_order')->orderBy('sort_order','desc')->get();
        if(count($last_data)>0)
        {
            $sort_order             = $last_data[0]->sort_order;
            $sort_order++;
           
            $mainData = array(
                    "title_type"            => $data->title_type,
                    "title"                 => $data->title,
                    "title_image"           => $data->title_image,
                    "response_type"         => $data->response_type,
                    "may_respond"           => $data->may_respond,
                    "may_select"            => $data->may_select,
                    "may_respond_count"     => $data->may_respond_count,
                    "may_select_count"      => $data->may_select_count,
                    "is_had_score"          => $data->is_had_score,
                    "is_multiple_type"      => $data->is_multiple_type,
                    "sort_order"            => $sort_order,
                    "option_text_type"      => $data->option_text_type,
                    "created_by"            => $data->created_by,
                    "last_change"           => date('Y-m-d H:i:s'),
                    "status"                => '2',
                    "total_responses"       => 0,
                    "ip_address"            => $request->ip(),
                );
            $activity_id = Activity::insertGetId($mainData);

            if($data->is_multiple_type=="1")
            {
                $MultipleoptionData = ActivityMultiple::where('activity_id',$id)->orderBy('sort_order','asc')->get();
                foreach ($MultipleoptionData as $value) 
                {
                    $saveData = array(
                        "activity_id"   => $activity_id,
                        "question"      => $value->question,
                        "sort_order"    => $value->sort_order,
                        "select_count"  => 0,
                        "ip_address"    => $request->ip(),
                    );
                    $sub_activity_id = ActivityMultiple::insertGetId($saveData);

                    $optionData = ActivityOption::where('activity_id',$id)->where('sub_activity_id',$value->id)->orderBy('sort_order','asc')->get();
                    foreach ($optionData as $value1) 
                    {
                        $saveData = array(
                            "activity_id"       => $activity_id,
                            "sub_activity_id"   => $sub_activity_id,
                            "sort_order"        => $value1->sort_order,
                            "is_correct"        => $value1->is_correct,
                            "score"             => $value1->score,
                            "option"            => $value1->option,
                            "option_image"      => $value1->option_image,
                            "answer_type"       => $value1->answer_type,
                            "select_count"      => 0,
                            "ip_address"        => $request->ip(),
                        );
                        ActivityOption::create($saveData);
                    }
                }
            }
            else
            {
                $optionData = ActivityOption::where('activity_id',$id)->orderBy('sort_order','asc')->get();
                foreach ($optionData as $value) 
                {
                    $saveData = array(
                        "activity_id"   => $activity_id,
                        "sort_order"    => $value->sort_order,
                        "is_correct"    => $value->is_correct,
                        "score"         => $value->score,
                        "option"        => $value->option,
                        "option_image"  => $value->option_image,
                        "answer_type"   => $value->answer_type,
                        "select_count"  => 0,
                        "ip_address"    => $request->ip(),
                    );

                    ActivityOption::create($saveData);
                }
            }

            return response()->json(["code"=> "200"],200);
        }
    }

    # List Trash
    public function list_trash(Request $request)
    {
        $trash_data = Activity::onlyTrashed()->orderBy('deleted_at','asc')->get();
        return view('admin.activity.trash',compact('trash_data'));
    }

    # Trash Action
    public function trash_action(Request $request)
    {
        $type = $request->type;
        $data = $request->data;
        foreach ($data as $value)
        {
            $id = app_decode($value);
            if($type==1)
            {
                Activity::where('id',$id)->restore();
            }
            else if($type==2)
            {
                Activity::where('id',$id)->forceDelete();
                ActivityMultiple::where('activity_id',$id)->forceDelete();
                ActivityOption::where('activity_id',$id)->forceDelete();
                ActivityResponse::where('activity_id',$id)->forceDelete();
            }
        }
        return response()->json(["code"=> "200"],200);
    }

    public function see_response($id='')
    {
        $id                 = app_decode($id);
        $activity_data      = Activity::where('id',$id)->get();

        if(count($activity_data)>0)
        {
            $responseTypeData   = ActivityResponse::select('response_from',DB::raw('sum(select_count) as total_select'))->where('activity_id',$id)->groupBy('response_from')->get();

            if($activity_data[0]->is_multiple_type==1)
            {
                $sub_activity_data  = ActivityMultiple::where('activity_id',$id)->orderBy('sort_order','asc')->get();
                $option_data        = array();
                foreach ($sub_activity_data as $value) 
                {
                    $temp['id']             = $value->id;
                    $temp['question']       = $value->question;
                    $temp['select_count']   = $value->select_count;

                    $SubActivitydata        = ActivityOption::where('activity_id',$id)->where('sub_activity_id',$value->id)->orderBy('sort_order','asc')->get();
                    
                    $options = array();
                    foreach ($SubActivitydata as $value1) 
                    {
                        $temp1                    = array();
                        $temp1['id']              = $value1->id;
                        $temp1['option']          = $value1->option;
                        $temp1['select_count']    = $value1->select_count;
                        $temp1['score']           = $value1->score;
                        $options[]                = $temp1;
                    }

                    $temp['options']    = $options;
                    $option_data[]      = $temp;
                }

                $responseAllData   = ActivityResponse::select('activity_options.option','activity_multiples.question','activity_response.unique_name','activity_response.updated_at','activity_response.response_from')->join('activity_options','activity_response.option_id','=','activity_options.id')->join('activity_multiples','activity_options.sub_activity_id','=','activity_multiples.id')->where('activity_response.activity_id',$id)->where('activity_response.select_count','>',0)->groupBy('activity_response.option_id','activity_response.visitor_id')->orderBy('activity_response.updated_at','desc')->get();

                return view('admin.activity.response_multiple',compact('activity_data','option_data','responseTypeData','responseAllData'));
            }
            else
            {
                $option_data        = ActivityOption::where('activity_id',$id)->orderBy('sort_order','asc')->get();
                
                $responseAllData   = ActivityResponse::select('activity_options.option','activity_response.unique_name','activity_response.updated_at','activity_response.response_from')->join('activity_options','activity_response.option_id','=','activity_options.id')->where('activity_response.activity_id',$id)->where('activity_response.select_count','>',0)->groupBy('activity_response.option_id','activity_response.visitor_id')->orderBy('activity_response.updated_at','desc')->get();

                return view('admin.activity.response',compact('activity_data','option_data','responseTypeData','responseAllData'));
            }
        }
    }

    public function see_graph_response($id='')
    {
        $admin_id           = Auth::user()->id;
        $id                 = app_decode($id);
        $activity_data      = Activity::where('id',$id)->get();
        
        if(count($activity_data)>0)
        {
            if($activity_data[0]->is_multiple_type==1)
            {
                $sub_activity_data  = ActivityMultiple::where('activity_id',$id)->orderBy('sort_order','asc')->get();
                $option_data        = array();
                foreach ($sub_activity_data as $value) 
                {
                    $temp['question_id']    = $value->id;
                    $temp['question_name']  = $value->question;
                    $SubActivitydata        = ActivityOption::where('activity_id',$id)->where('sub_activity_id',$value->id)->orderBy('sort_order','asc')->get();
                    $options = array();
                    foreach ($SubActivitydata as $value1) 
                    {
                        $options[] = $value1->option;
                    }
                    $temp['options']    = $options;
                    $option_data[]      = $temp;
                }
                return view('admin.activity.graph_response_multiple',compact('activity_data','sub_activity_data','option_data','admin_id'));
            }
            else
            {
                $option_data        = ActivityOption::where('activity_id',$id)->orderBy('sort_order','asc')->get();
                return view('admin.activity.graph_response',compact('activity_data','option_data','admin_id'));
            }
        }
    }

    public function print_response($id='')
    {
        $id                 = app_decode($id);
        $activity_data      = Activity::where('id',$id)->first();
        $responseTypeData   = ActivityResponse::select('response_from',DB::raw('sum(select_count) as total_select'))->where('activity_id',$id)->groupBy('response_from')->get();

        if($activity_data->is_multiple_type=='1')
        {
            $sub_activity_data  = ActivityMultiple::where('activity_id',$id)->orderBy('sort_order','asc')->get();
            $option_data        = array();
            foreach ($sub_activity_data as $value) 
            {
                $temp['id']             = $value->id;
                $temp['question']       = $value->question;
                $temp['select_count']   = $value->select_count;

                $SubActivitydata        = ActivityOption::where('activity_id',$id)->where('sub_activity_id',$value->id)->orderBy('sort_order','asc')->get();
                
                $options = array();
                foreach ($SubActivitydata as $value1) 
                {
                    $temp1                    = array();
                    $temp1['id']              = $value1->id;
                    $temp1['option']          = $value1->option;
                    $temp1['select_count']    = $value1->select_count;
                    $temp1['score']           = $value1->score;
                    $options[]                = $temp1;
                }

                $temp['options']    = $options;
                $option_data[]      = $temp;
            }
            $responseAllData   = ActivityResponse::select('activity_options.option','activity_multiples.question','activity_response.unique_name','activity_response.updated_at','activity_response.response_from')->join('activity_options','activity_response.option_id','=','activity_options.id')->join('activity_multiples','activity_options.sub_activity_id','=','activity_multiples.id')->where('activity_response.activity_id',$id)->where('activity_response.select_count','>',0)->groupBy('activity_response.option_id','activity_response.visitor_id')->orderBy('activity_response.updated_at','desc')->get();
            return view('admin.activity.print_multiple',compact('activity_data','option_data','responseTypeData','responseAllData'));
        }
        else
        {
            $option_data        = ActivityOption::where('activity_id',$id)->orderBy('sort_order','asc')->get();
            $responseAllData   = ActivityResponse::join('activity_options','activity_response.option_id','=','activity_options.id')->select('activity_options.option','activity_response.unique_name','activity_response.updated_at','activity_response.response_from')->where('activity_response.activity_id',$id)->where('activity_response.select_count','>',0)->groupBy('activity_response.option_id','activity_response.visitor_id')->orderBy('activity_response.updated_at','desc')->get();
            return view('admin.activity.print',compact('activity_data','option_data','responseTypeData','responseAllData'));
        }
    }

    public function share_responses(Request $request)
    {
        $validatedData = $request->validate([
        'email'         => [
            'required_if:response_via,==,email',
            function ($attribute, $value, $fail) {
                $emails = array_map('trim', explode(',', $value));
                $validator = Validator::make(['emails' => $emails], ['emails.*' => 'required_if:response_via,==,email|email']);
                if ($validator->fails()) {
                $fail('All email addresses must be valid.');
                }
            },
        ],
        'response_via' => 'required'
        ], [
            'message.required' => 'Please provide valid message.',
            'message.min' => 'Message must be greater than or equal to 10 characters.',
            'message.max' => 'Message must be less than or equal to 2000 characters.',
        ]);

        $id                 = app_decode($request->id);
        $emails             = $request->email;
        $response_via       = $request->response_via;

        $activity_data      = Activity::select('title','total_responses')->where('id',$id)->first();
        $allActivityOptions = ActivityOption::where('activity_id',$id)->orderBy('sort_order','asc')->get();
        if($response_via=="email")
        {
            $responseAllData = array(
                "activity_data"         => $activity_data,
                "activity_options"      => $allActivityOptions
            );
            $email_array    = explode(',', $emails);
            foreach ($email_array as $email) 
            {
                $ResponseData    = ActivityOption::select('activity_response.select_count','activity_options.option_name','activity_options.option','activity_options.option_image','activity_options.score')->join('activity_response','activity_response.option_id','=','activity_options.id')->join('visitors','visitors.id','=','activity_response.visitor_id')->where('activity_response.activity_id',$id)->where('visitors.email',$email)->get();
                $total_response         = 0;
                $induvitual_response    = array();
                foreach ($ResponseData as $key => $value) 
                {
                    $total_response += $value->select_count;
                }

                foreach ($ResponseData as $key => $value) 
                {
                    $value->total_response = $total_response;
                    $induvitual_response[] = $value;
                }

                $mailContent = array(
                    "email"                 => $email,
                    "responseData"          => $responseAllData,
                    "induvitual_response"   => $induvitual_response,
                );

                $mailData = [
                    'subject'   => 'Activity Result',
                    'view'      => 'admin.email.share_responses',
                    'data'      => $mailContent,
                ];
                try {
                    Mail::to($email)->send(new CustomSendMail($mailData));
                    return redirect()->back()->with('success',trans('admin.share_responses_msg'));
                } catch (Exception $e) {

                    return redirect()->back()->withErrors($e->getMessage());
                }
            }
        }
        else if($response_via=="sms")
        {
            if($activity_data)
            {
                $message = "";
                $message .= trans('admin.share_response_content');
                $message .= "\n".$activity_data->title;
                $message .= "\n\n";
                $message .= trans('admin.overall_response_content');
                $message .= "\n\n";

                foreach ($allActivityOptions as $value) 
                {
                    $pecentage = 0;
                    if(isset($activity_data->total_responses))
                    {
                        if($activity_data->total_responses>0)
                        {
                            $pecentage = round(($value->select_count/$activity_data->total_responses)*100);
                        }
                        if($value->option_name)
                        {
                            $message .= $value->option_name.")";
                        }
                        $message .= ' '.$value->option;
                        $message .= ' ('.trans("admin.score").' : '.($value->score*$value->select_count).')';
                        $message .= ' '.$pecentage." %\n";
                    }
                }

                $VisitorsData = Visitors::select('id','phone_number')->where('phone_number','!=','')->get();
                foreach ($VisitorsData as $visitors_value) 
                {
                    $ResponseData    = ActivityOption::select('activity_response.select_count','activity_options.option_name','activity_options.option','activity_options.option_image','activity_options.score')->join('activity_response','activity_response.option_id','=','activity_options.id')->join('visitors','visitors.id','=','activity_response.visitor_id')->where('activity_response.activity_id',$id)->where('visitors.id',$visitors_value->id)->get();
                    $total_response         = 0;
                    $induvitual_response    = array();
                    foreach ($ResponseData as $key => $value) 
                    {
                        $total_response += $value->select_count;
                    }

                    foreach ($ResponseData as $key => $value) 
                    {
                        $value->total_response = $total_response;
                        $induvitual_response[] = $value;
                    }

                    $message .= "\n\n";
                    $message .= trans('admin.induvitual_response_content');
                    $message .= "\n\n";

                    foreach ($induvitual_response as $value1) 
                    {
                        $pecentage = 0;
                        if(isset($value1->total_response))
                        {
                            if($value1->total_response>0)
                            {
                                $pecentage = round(($value1->select_count/$value1->total_response)*100);
                            }
                            if($value1->option_name)
                            {
                                $message .= $value1->option_name.")";
                            }
                            $message .= ' '.$value1->option;
                            $message .= ' ('.trans("admin.score").' : '.($value1->score*$value1->select_count).')';
                            $message .= ' '.$pecentage." %\n";
                        }
                    }

                    $twillio_from   = Cache::get('TWILIO_FROM');
                    $twillio_sid    = Cache::get('TWILIO_SECRET');
                    $twillio_token  = Cache::get('TWILIO_TOKEN');
                    try 
                    {
                        $client = new TwilioClient($twillio_sid, $twillio_token);
                        if($visitors_value->phone_number!="" && $message!="")
                        {
                            $client->messages->create($visitors_value->phone_number, [
                                'from' => $twillio_from,
                                'body' => $message
                            ]);
                        }
                        return redirect()->back()->with('success',trans('admin.share_responses_msg'));
                    } 
                    catch (Exception $e) 
                    {
                        return $e->getMessage();
                    }
                }
            }
        }
    }

    public function download_excel($id='')
    {
        $id                 = app_decode($id);
        $activity_data      = Activity::where('id',$id)->first();
        $responseTypeData   = ActivityResponse::select('response_from',DB::raw('sum(select_count) as total_select'))->where('activity_id',$id)->groupBy('response_from')->get();

        $csv                = Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(array($activity_data->title));
        $csv->insertOne(array(trans('admin.summary')));
        $header             = array();

        if($activity_data->is_multiple_type=="1")
        {
            $header[]   = trans('admin.question');
            $header[]   = trans('admin.response');
            if($activity_data->is_had_score==1)
            {
               $header[] = trans('admin.score'); 
            }
            $header[] = trans('admin.count');
            if($activity_data->is_had_score==1)
            {
               $header[] = trans('admin.total_score'); 
            }
            $csv->insertOne($header);

            $total_count        = 0;
            $total_score        = 0;
            $total_final_score  = 0;

            $sub_activity_data  = ActivityMultiple::where('activity_id',$id)->orderBy('sort_order','asc')->get();
            $option_data        = array();
            foreach ($sub_activity_data as $value) 
            {
                $SubActivitydata        = ActivityOption::where('activity_id',$id)->where('sub_activity_id',$value->id)->orderBy('sort_order','asc')->get();
                $options = array();
                foreach ($SubActivitydata as $value1) 
                {
                    $body   = array();
                    $body[] = $value->question;
                    $body[] = $value1->option;
                    if($activity_data->is_had_score==1)
                    {
                       $body[] = $value1->score; 
                    }
                    if($value1->select_count>0)
                    {
                        $body[] = $value1->select_count;
                    }
                    else
                    {
                        $body[] = 0;
                    }
                    
                    if($activity_data->is_had_score==1)
                    {
                        if($value1->select_count>0)
                        {
                            $body[] = ($value1->score)*($value1->select_count);
                        }
                        else
                        {
                            $body[] = 0;
                        }
                    }
                    $csv->insertOne($body);
                    if($value1->select_count>0)
                    {
                        $total_count         += $value1->select_count;
                        $total_score         += ($value1->score)*($value1->select_count);
                        $total_final_score   += ($value1->score)*($value1->select_count);
                    }
                }
            }

            $footer     = array();
            $footer[]   = "";
            $footer[]   = trans('admin.total');

            if($activity_data->is_had_score==1)
            {
               $footer[] = ''; 
            }
            
            if($activity_data->is_had_score==1)
            {
               $footer[] = $total_count;
               $footer[] = $total_final_score;
            }
            else
            {
                $footer[] = $total_count;
            }
            $csv->insertOne($footer);

            $csv->insertOne(array());
            $csv->insertOne(array());

            $header     = array();
            $header[]   = trans('admin.how_people_responded');
            $header[]   = trans('admin.count');
            $csv->insertOne($header);

            $total_count = 0;
            foreach($responseTypeData as $value)
            {
                $body   = array();
                $body[] = $value->response_from;
                $body[] = $value->total_select;
                $csv->insertOne($body);
                $total_count += $value->total_select;
            }

            $footer     = array();
            $footer[]   = trans('admin.total');
            $footer[]   = $total_count;
            $csv->insertOne($footer);

            $csv->insertOne(array());
            $csv->insertOne(array());
            
            $csv->insertOne(array(trans('admin.indivitual_responses')));

            $header     = array();
            // $header[]   = trans('admin.question');
            $header[]   = trans('admin.response');
            $header[]   = trans('admin.via');
            $header[]   = trans('admin.screen_name');
            $header[]   = trans('admin.email');
            $header[]   = trans('admin.phone_number');
            $header[]   = trans('admin.received_at');
            $csv->insertOne($header);
            
            $responseAllData   = ActivityResponse::select('activity_options.option','activity_multiples.question','activity_response.unique_name','activity_response.updated_at','activity_response.response_from','visitors.email','visitors.phone_number')->join('activity_options','activity_response.option_id','=','activity_options.id')->join('activity_multiples','activity_options.sub_activity_id','=','activity_multiples.id')->join('visitors','activity_response.visitor_id','=','visitors.id')->where('activity_response.activity_id',$id)->where('activity_response.select_count','>',0)->groupBy('activity_response.option_id','activity_response.visitor_id')->orderBy('activity_response.updated_at','desc')->get();
            foreach($responseAllData as $value)
            {
                $body   = array();
                // $body[] = $value->question;
                $body[] = $value->option;
                $body[] = $value->response_from;
                $body[] = $value->unique_name;
                $body[] = $value->email;
                $body[] = $value->phone_number;
                $body[] = app_date_format($value->updated_at);
                $csv->insertOne($body);
            }
        }
        else
        {
            $header[]   = trans('admin.response');
            if($activity_data->is_had_score==1)
            {
               $header[] = trans('admin.score'); 
            }
            $header[] = trans('admin.count');
            if($activity_data->is_had_score==1)
            {
               $header[] = trans('admin.total_score'); 
            }
            $csv->insertOne($header);

            $total_count        = 0;
            $total_score        = 0;
            $total_final_score  = 0;

            $option_data        = ActivityOption::where('activity_id',$id)->orderBy('sort_order','asc')->get();
            foreach ($option_data as $value) 
            {
                $body   = array();
                $body[] = $value->option;
                if($activity_data->is_had_score==1)
                {
                   $body[] = $value->score; 
                }

                if($value->select_count>0)
                {
                    $body[] = $value->select_count;
                }
                else
                {
                    $body[] = 0;
                }
                
                if($activity_data->is_had_score==1)
                {
                    if($value->select_count>0)
                    {
                        $body[] = ($value->score)*($value->select_count); 
                    }
                    else
                    {
                        $body[] = 0; 
                    }
                   
                }
                $csv->insertOne($body);
                if($value->select_count>0)
                {
                    $total_count         += $value->select_count;
                    $total_score         += ($value->score)*($value->select_count);
                    $total_final_score   += ($value->score)*($value->select_count);
                }
            }

            $footer = array();
            $footer[] = trans('admin.total');
            if($activity_data->is_had_score==1)
            {
               $footer[] = ''; 
            }
            
            if($activity_data->is_had_score==1)
            {
               $footer[] = $total_count;
               $footer[] = $total_final_score;
            }
            else
            {
                $footer[] = $total_count;
            }
            $csv->insertOne($footer);

            $csv->insertOne(array());
            $csv->insertOne(array());

            $header     = array();
            $header[]   = trans('admin.how_people_responded');
            $header[]   = trans('admin.count');
            $csv->insertOne($header);

            $total_count = 0;
            foreach($responseTypeData as $value)
            {
                $body   = array();
                $body[] = $value->response_from;
                $body[] = $value->total_select;
                $csv->insertOne($body);
                $total_count += $value->total_select;
            }

            $footer     = array();
            $footer[]   = trans('admin.total');
            $footer[]   = $total_count;
            $csv->insertOne($footer);

            $csv->insertOne(array());
            $csv->insertOne(array());
            
            $csv->insertOne(array(trans('admin.indivitual_responses')));

            $header     = array();
            $header[]   = trans('admin.response');
            $header[]   = trans('admin.via');
            $header[]   = trans('admin.screen_name');
            $header[]   = trans('admin.email');
            $header[]   = trans('admin.phone_number');
            $header[]   = trans('admin.received_at');
            $csv->insertOne($header);

            $responseAllData    = ActivityResponse::join('activity_options','activity_response.option_id','=','activity_options.id')->join('visitors','activity_response.visitor_id','visitors.id')->select('activity_options.option','activity_response.unique_name','activity_response.updated_at','activity_response.response_from','visitors.email','visitors.phone_number')->where('activity_response.activity_id',$id)->where('activity_response.select_count','>',0)->groupBy('activity_response.option_id','activity_response.visitor_id')->orderBy('activity_response.updated_at','desc')->get();
            foreach($responseAllData as $value)
            {
                $body   = array();
                $body[] = $value->option;
                $body[] = $value->response_from;
                $body[] = $value->unique_name;
                $body[] = $value->email;
                $body[] = $value->phone_number;
                $body[] = app_date_format($value->updated_at);
                $csv->insertOne($body);
            }
        }
    

        $filename = str_replace('.','',str_replace(' ', '_', $activity_data->title));
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
        ];

        $response = Response::make($csv->getContent(), 200, $headers);

        return $response;
    }

    public function clear_activity_response(Request $request)
    {
        $id     = app_decode($request->ref);
        ActivityResponse::where('activity_id',$id)->forceDelete();
        ActivityOption::where('activity_id',$id)->update(array('select_count' => 0));
        ActivityMultiple::where('activity_id',$id)->update(array('select_count' => 0));
        Activity::where('id',$id)->update(array('total_responses' => 0));
        return response()->json(["code"=> "200"],200);
    }

    public function generate_question_set($question, $options, $answer_index) {
        return [
            "question" => $question,
            "options" => $options,
            "answer" => $answer_index
        ];
    }

    public function load_activity_data(Request $request)
    {
        $question_sets = [];
        for ($i = $request->from; $i <= $request->to; $i++) {
            $question = "Question $i?";
            $options = ["Option A", "Option B", "Option C", "Option D"];
            $answer_index = rand(0, count($options) - 1);
            $question_sets[] = $this->generate_question_set($question, $options, $answer_index);
        }

        $json_data = json_encode($question_sets, JSON_PRETTY_PRINT);
 
        $array = json_decode($json_data);

        # Create a visitors
        if($request->from==1)
        {
            for ($i=0; $i < 250; $i++) 
            { 
                $unique_name = $this->get_guest_name();
                Session::put('unique_name',$unique_name);
                
                $save_data = array(
                
                    "unique_name"       => $unique_name,
                    "ip_address"        => $request->ip(),

                );
                $visitor_id = Visitors::insertGetId($save_data);
            }
        }
        

        foreach ($array as $key => $value) 
        {
            $sort_order = 1;
            $last_data  = Activity::select('sort_order')->orderBy('sort_order','desc')->get();
            if(count($last_data)>0)
            {
                $sort_order             = $last_data[0]->sort_order;
                $sort_order++;
            }

            $mainData = array(
                "title_type"            => 1,
                "title"                 => $value->question,
                "response_type"         => 1,
                "may_respond"           => 1,
                "may_select"            => 1,
                "may_respond_count"     => 1,
                "may_select_count"      => 1,
                "is_had_score"          => 2,
                "sort_order"            => $sort_order,
                "status"                => '2',
                "total_responses"       => 1,
                "ip_address"            => $request->ip(),
            );
            $activity_id = Activity::insertGetId($mainData);

            $options_data   = $value->options;
            $count          = 0;
            foreach ($options_data as $key => $value0) 
            {
                $is_correct     = 2;
                $select_count   = 0;
                if($count==0)
                {
                    $is_correct     = 1;
                    $select_count   = 1;
                    $count++;
                }
                $saveData = array(
                    "activity_id"   => $activity_id,
                    "sort_order"    => $key+1,
                    "is_correct"    => $is_correct,
                    "score"         => 0,
                    "option"        => $value0,
                    "answer_type"   => 1,
                    "select_count"  => $select_count,
                    "ip_address"    => $request->ip(),
                );
                
                $option_id = ActivityOption::insertGetId($saveData);
                if($select_count==1)
                {
                    $VisitorsData = Visitors::orderBy('id','asc')->get();
                    foreach ($VisitorsData as $key => $value1) 
                    {
                        $insert_array = array(

                        "activity_id"       => $activity_id,
                        "visitor_id"        => $value1->id,
                        "option_id"         => $option_id,
                        "select_count"      => 1,
                        "response_from"     => url('/').'/visit/'.Auth::user()->username,
                        "unique_name"       => $value1->unique_name,
                        "ip_address"        => $request->ip(),

                        );
                        ActivityResponse::create($insert_array);
                    }
                }
            }
        }
        return response()->json(["code"=> 200],200);
    }

    # Get is exist guest
    public function get_guest_name($unique_name="")
    {
        $exist_count    = Visitors::where('unique_name',$unique_name)->count();
        if($exist_count>0 || $unique_name=="")
        {
            $unique_name    = "Guest_".rand(1,9999999999);
            $this->get_guest_name($unique_name);
        }
        return $unique_name;
    }

    public function poll_result($id)
    {
        $id                 = app_decode($id);
        $activity_data      = Activity::where('id',$id)->get();
        if(count($activity_data)>0)
        {
            $admin_id   = $activity_data[0]->created_by;
            $userData   = User::where('id',$admin_id)->get();
            if(count($userData)>0)
            {
                $url            = $userData[0]->polling_url;
                $number         = $userData[0]->phone_number;
                $code           = $userData[0]->phone_code;
                $username       = $userData[0]->username;

                $phone_number = '';
                $general_data = GeneralSettings::all();
                if(count($general_data)>0)
                {
                    $from_code      = $general_data[0]->twilio_from_code;
                    $from_number    = $general_data[0]->twilio_from_number;
                    $phone_number   = '+'.$from_code.$from_number;
                }

                $option_data        = ActivityOption::where('activity_id',$id)->orderBy('sort_order','asc')->get();
                return view('admin.poll_response',compact('activity_data','option_data','admin_id','url','phone_number','username'));
            }
        }
        
    }

    public function poll_result_multiple($id,$sub_id)
    {
        $id                 = app_decode($id);
        $question_id        = app_decode($sub_id);
        $activity_data      = Activity::where('id',$id)->get();
        if(count($activity_data)>0)
        {
            $admin_id   = $activity_data[0]->created_by;
            $userData   = User::where('id',$admin_id)->get();
            if(count($userData)>0)
            {
                $url            = $userData[0]->polling_url;
                $number         = $userData[0]->phone_number;
                $code           = $userData[0]->phone_code;
                $username       = $userData[0]->username;

                $phone_number = '';
                $general_data = GeneralSettings::all();
                if(count($general_data)>0)
                {
                    $from_code      = $general_data[0]->twilio_from_code;
                    $from_number    = $general_data[0]->twilio_from_number;
                    $phone_number   = '+'.$from_code.$from_number;
                }
                return view('admin.poll_response_multiple',compact('activity_data','admin_id','url','phone_number','username','question_id'));
            }
        }
        
    }

    public function send_polling_results(Request $request)
    {
        $amount_per_score = 0;
        $ActivitySettings = ActivitySettings::select('amount_per_score')->get();
        if(count($ActivitySettings)>0)
        {
            $amount_per_score = $ActivitySettings[0]->amount_per_score;
        }

        $latestActivityData = Activity::select('id','title','is_multiple_type')->where('is_had_score',1)->orderBy('sort_order','asc')->get();
        $visitors_array     = array();
        $activity_sno = 1;
        foreach ($latestActivityData as $value_1) 
        {
            $visitorsData = ActivityResponse::select('activity_response.visitor_id','visitors.email','visitors.phone_number')->join('visitors','activity_response.visitor_id','visitors.id')->where('activity_response.activity_id',$value_1->id)->groupBy('activity_response.visitor_id')->get();

            foreach ($visitorsData as $value_2) 
            {
                $activity_id        = $value_1->id;
                $is_multiple_type   = $value_1->is_multiple_type;
                $title              = $value_1->title;
                $visitor_id         = $value_2->visitor_id;
                $email              = $value_2->email;
                $phone_number       = $value_2->phone_number;

                $total_score        = 0;
                $total_amount       = 0;

                if(!isset($visitors_array['visitors_'.$visitor_id]['total_amount']))
                {
                    $visitors_array['visitors_'.$visitor_id]['total_amount'] = 0;
                }
                if(!isset($visitors_array['visitors_'.$visitor_id]['total_score']))
                {
                    $visitors_array['visitors_'.$visitor_id]['total_score'] = 0;
                }

                $temp_message       = array();
                $temp_message[]     = "";
                $temp_message[]     = $activity_sno.') '.$title;

                $temp_smsmessage       = array();
                $temp_smsmessage[]     = "";
                $temp_smsmessage[]     = "";
                $temp_smsmessage[]     = $activity_sno.') '.$title;
                $temp_smsmessage[]     = "";

                if($is_multiple_type=="2" || $is_multiple_type==2)
                {
                    $PollResult = ActivityResponse::select('activity_options.option','activity_options.option_name','activity_options.sort_order','activity_options.score','activity_options.select_count')->join('activity_options','activity_response.option_id','activity_options.id')->where('activity_response.activity_id',$activity_id)->where('activity_response.visitor_id',$visitor_id)->where('activity_response.select_count','>',0)->get();
                    $temp_message[] = "";
                    $temp_smsmessage[]     = "";
                    foreach ($PollResult as $value_3) 
                    {
                        $current_score          = ($value_3->score*$value_3->select_count);
                        $total_score            += ($value_3->score*$value_3->select_count);
                        $total_amount           += (($value_3->score*$value_3->select_count)*$amount_per_score);
                        $temp_message[]         = $value_3->option.' : +('.$current_score.') Days/Million$';
                        if($value_3->option_name=="")
                        {
                            $temp_smsmessage[]  = 'Choice no '.$value_3->sort_order.' : +('.$current_score.') Days/Million$';
                        }
                        else
                        {
                            $temp_smsmessage[]  = 'Choice no '.$value_3->option_name.' : +('.$current_score.') Days/Million$';
                        }
                        $temp_smsmessage[]      = "";
                    }
                }
                else
                {
                    $PollResult = ActivityResponse::select('activity_options.option','activity_options.option_name','activity_options.sort_order','activity_multiples.question','activity_options.score','activity_options.select_count')->join('activity_options','activity_response.option_id','activity_options.id')->join('activity_multiples','activity_response.sub_activity_id','activity_multiples.id')->where('activity_response.activity_id',$activity_id)->where('activity_response.visitor_id',$visitor_id)->where('activity_response.select_count','>',0)->get();

                    $is_added   = 0;
                    foreach ($PollResult as $value_4) 
                    {
                        $current_score      = ($value_4->score*$value_4->select_count);
                        $total_score        += ($value_4->score*$value_4->select_count);
                        $temp_message[]     = "";
                        $temp_message[]     = $value_4->question;
                        $temp_smsmessage[]  = "";
                        $temp_smsmessage[]  = "";
                        $temp_smsmessage[]  = $value_4->question;
                        $temp_smsmessage[]  = "";
                        $temp_smsmessage[]  = "";
                        $total_amount       += (($value_4->score*$value_4->select_count)*$amount_per_score);
                        $temp_message[]     = $value_4->option.' : +('.$current_score.') Days/Million$';
                        if($value_4->option_name=="")
                        {
                            $temp_smsmessage[]  = 'Choice no '.$value_4->sort_order.' : +('.$current_score.') Days/Million$';
                        }
                        else
                        {
                            $temp_smsmessage[]  = 'Choice no '.$value_4->option_name.' : +('.$current_score.') Days/Million$';
                        }
                    }
                }

                $visitors_array['visitors_'.$visitor_id]['data'][]          = $temp_message;
                $visitors_array['visitors_'.$visitor_id]['sms_data'][]      = $temp_smsmessage;
                $visitors_array['visitors_'.$visitor_id]['total_amount']    += $total_amount;
                $visitors_array['visitors_'.$visitor_id]['total_score']     += $total_score;
            }
            $activity_sno++;
        }

        $twillio_from   = Cache::get('TWILIO_FROM');
        $twillio_sid    = Cache::get('TWILIO_SECRET');
        $twillio_token  = Cache::get('TWILIO_TOKEN');

        foreach ($visitors_array as $key => $value) 
        {
            $visitors_explode = explode('_',$key);
            if(count($visitors_explode)>0)
            {
                $visitor_id     = $visitors_explode[1];
                $visitorDetails = Visitors::select('email','phone_number')->where('id',$visitor_id)->get();
                if(count($visitorDetails)>0)
                {
                    $email          = $visitorDetails[0]->email;
                    $phone_number   = $visitorDetails[0]->phone_number;

                    $message            = array();
                    $message[]          = "Thank you for your participation in ".ucfirst(Auth::user()->username)."'s RanSim (ransomware simulation):";
                    $message[]          = "Based On Your Decisions, You Added:  (".$value['total_score'].") days resulting in $(".$value['total_amount'].") millions of additional business interruption costs.";
                    $message[]          = $value['data'];
                    $message[]          = "";
                    $message[]          = "If you'd like to learn more about how to lower Business Interruption costs or Fenix24, reach out to us at:  ransim@fenix24.com";


                    $smsmessage            = array();
                    $smsmessage[]          = "Thank you for your participation in ".ucfirst(Auth::user()->username)."'s RanSim (ransomware simulation):";
                    $smsmessage[]          = "";
                    $smsmessage[]          = "Based On Your Decisions, You Added:  (".$value['total_score'].") days resulting in $(".$value['total_amount'].") millions of additional business interruption costs.";
                    $smsmessage[]          = $value['sms_data'];
                    $smsmessage[]          = "";
                    $smsmessage[]          = "If you'd like to learn more about how to lower Business Interruption costs or Fenix24, reach out to us at:  ransim@fenix24.com";

                    if($email !="")
                    {
                        $mail_message = array();
                        foreach ($message as $value_sms) 
                        {
                            if(is_array($value_sms))
                            {
                                foreach ($value_sms as $value_sub) 
                                {
                                    foreach ($value_sub as $valuetemp) 
                                    {
                                        $mail_message[] = $valuetemp;
                                    }
                                }
                            }
                            else
                            {
                                $mail_message[] = $value_sms;
                            }
                        }
                        $mailContent = array(
                            "email"            => $email,
                            "message"          => $mail_message,
                        );

                        $mailData = [
                            'subject'   => 'Your Score',
                            'view'      => 'admin.email.share_responses',
                            'data'      => $mailContent,
                        ];
                        try {
                            Mail::to($email)->send(new CustomSendMail($mailData));
                        } catch (Exception $e) {

                            return redirect()->back()->withErrors($e->getMessage());
                        }
                    }

                    if($phone_number !="")
                    {
                        $mail_message = "";
                        foreach ($smsmessage as $value_sms) 
                        {
                            if(is_array($value_sms))
                            {
                                foreach ($value_sms as $value_sub) 
                                {
                                    foreach ($value_sub as $valuetemp) 
                                    {
                                        if($valuetemp=="")
                                        {
                                            $mail_message .= "\n";
                                        }
                                        else
                                        {
                                            $mail_message .= $valuetemp;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                if($value_sms=="")
                                {
                                    $mail_message .= "\n";
                                }
                                else
                                {
                                    $mail_message .= $value_sms;
                                }
                            }
                        }

                        try 
                        {
                            $client = new TwilioClient($twillio_sid, $twillio_token);
                            if($mail_message!="")
                            {
                                $client->messages->create($phone_number, [
                                    'from' => $twillio_from,
                                    'body' => $mail_message
                                ]);
                            }
                        } 
                        catch (Exception $e) 
                        {
                            return redirect()->back()->withErrors($e->getMessage());
                        }

                        // if(strlen($mail_message)>1300)
                        // {
                        //     $count  = ceil(strlen($mail_message)/1300);
                        //     $from   = 0;
                        //     $to     = 1300;
                        //     for ($i=0; $i < $count; $i++) 
                        //     { 
                        //         $message = substr($mail_message, $from,$to);
                        //         try 
                        //         {
                        //             $client = new TwilioClient($twillio_sid, $twillio_token);
                        //             if($message!="")
                        //             {
                        //                 $client->messages->create($phone_number, [
                        //                     'from' => $twillio_from,
                        //                     'body' => $message
                        //                 ]);
                        //                 $from   += $to+1;
                        //                 $to     += $to+1300;
                        //             }
                        //         } 
                        //         catch (Exception $e) 
                        //         {
                        //             return redirect()->back()->withErrors($e->getMessage());
                        //         }
                        //     }
                        // }
                        // else
                        // {
                        //     try 
                        //     {
                        //         $client = new TwilioClient($twillio_sid, $twillio_token);
                        //         if($mail_message!="")
                        //         {
                        //             $client->messages->create($phone_number, [
                        //                 'from' => $twillio_from,
                        //                 'body' => $mail_message
                        //             ]);
                        //         }
                        //     } 
                        //     catch (Exception $e) 
                        //     {
                        //         return redirect()->back()->withErrors($e->getMessage());
                        //     }
                        // }
                    }
                }
            }
        }
        return redirect()->back()->with('success',trans('admin.share_responses_msg'));
    }

    public function socket_testing(Request $request)
    {
        return view('admin.socket_testing');
    }
}
