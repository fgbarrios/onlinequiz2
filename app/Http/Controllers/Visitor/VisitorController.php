<?php

namespace App\Http\Controllers\Visitor;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitors;
use App\Models\Activity;
use App\Models\ActivityMultiple;
use App\Models\ActivityOption;
use App\Models\ActivityResponse;
use Auth;
use Hash;
use Session;
use Validator;
use DB;
use Cache;
use Twilio\Rest\Client as TwilioClient;
use Twilio\TwiML\MessagingResponse;
use WebSocket\Client;

class VisitorController extends Controller
{
    # Visti page
    public function visit($username="")
    {
        $userdata   = User::where('username',$username)->get();
        if(count($userdata)>0)
        {
            $username   = $userdata[0]->username;
            $name       = "";
            if(Session::get('unique_name'))
            {
                $name = Session::get('unique_name');
                return view('visitors.main',compact('name','username'));
            }
            else
            {
                return view('visitors.details',compact('name','username'));
            }
        }
        else
        {
            return redirect('/');
        }
    }

    # Vistior vote page
    public function vote_page(Request $request)
    {
        $userdata       = User::first();
        $username       = "";
        if($userdata->username)
        {
            $username = $userdata->username;
        }
        else
        {
            return redirect('/');
        }

        if(!Session::get('unique_name'))
        {
            $unique_name = $this->get_guest_name();
            Session::put('unique_name',$unique_name);
            
            $save_data = array(
            
                "unique_name"       => $unique_name,
                "ip_address"        => $request->ip(),

            );
            $visitor_id = Visitors::insertGetId($save_data);
            Session::put('visitor_id',$visitor_id);
        }

        $name           = Session::get('unique_name');
        $activity_data  = Activity::where('status','1')->orderBy('sort_order','asc')->get();
        if(count($activity_data)>0)
        {
            $activity_data  = $activity_data[0];
            
            $optionData                 = ActivityOption::select('id','option','option_image')->where('activity_id',$activity_data->id)->orderBy('sort_order','asc')->get();

            $total_count                = 0;
            foreach ($optionData as $value) 
            {
                $responseData   = ActivityResponse::select(DB::raw('COUNT(id) as total_select'))->where('activity_id',$activity_data->id)->where('option_id',$value->id)->where('visitor_id',Session::get('visitor_id'))->get();
                $total_select   = 0;
                if(count($responseData)>0)
                {
                    $total_select = $responseData[0]->total_select;
                }

                $total_count                += $total_select;

                $temp = [];
                $temp['may_select_count']   = $activity_data->may_select_count;
                $temp['available_select']   = ($activity_data->may_select_count-$total_select);
                $temp['total_select']       = $total_select;
                $temp['option_id']          = $value->id;
                $temp['option_text']        = $value->option;
                $temp['option_image']       = $value->option_image;
                $response_data['option_id_'.$value->id] = $temp;
            }

            $activity_data->total_count = $total_count;
            $option_data    = ActivityOption::where('activity_id',$activity_data->id)->orderBy('sort_order','asc')->get();
            
            return redirect()->route('visit',['username' => $username]);
        }
        else
        {
            return redirect()->route('visit',['username' => $username]);
        }
    }

    # Save visitors details
    public function save_visitors_details(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name'          => 'required|string',
            'email'         => 'required_if:phone_number,null',
            'phone_number'  => 'required_if:email,null',
        ],[
            'email.required_if' => 'Email or Phone number is required',
            'phone_number.required_if' => 'Email or Phone number is required',
        ]);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors());
        } 

        $unique_name    = $request->name;
        $phone_number   = $request->phone_number;
        
        $save_data = array(
            "name"              => $request->name,
            "email"             => $request->email,
            "phone_number"      => $phone_number,
            "unique_name"       => $unique_name,
            "ip_address"        => $request->ip(),
            "browser_agent"     => $request->header('user-agent'),
        );
        $visitor_id = Visitors::insertGetId($save_data);

        Session::put('unique_name',$unique_name);
        Session::put('visitor_id',$visitor_id);

        return redirect()->route('vote-page');
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

    # Add selected options

    public function add_selected_option($activity_id="",$sub_activity_id="",$option_id="",$ip="")
    {
        $userdata       = User::first();
        $polling_url    = "";
        if($userdata->polling_url)
        {
            $polling_url = $userdata->polling_url;
        }

        $activity_data      = Activity::find($activity_id);
        $select_count       = $activity_data->total_responses+1;
        if($select_count<0)
        {
            $select_count = 0;
        }
        $activity_data->total_responses = $select_count;
        $activity_data->save();

        if($sub_activity_id !="")
        {
            $sub_activity_data   = ActivityMultiple::where("activity_id",$activity_id)->where("id",$sub_activity_id)->first();
            $select_count       = $sub_activity_data->select_count+1;
            if($select_count<0)
            {
                $select_count = 0;
            }
            $sub_activity_data->select_count = $select_count;
            $sub_activity_data->save();

            $option_data   = ActivityOption::where("activity_id",$activity_id)->where("sub_activity_id",$sub_activity_id)->where("id",$option_id)->first();
            $select_count       = $option_data->select_count+1;
            if($select_count<0)
            {
                $select_count = 0;
            }
            $option_data->select_count = $select_count;
            $option_data->save();

            $is_exist = ActivityResponse::select('select_count')->where('activity_id',$activity_id)->where('sub_activity_id',$sub_activity_id)->where('option_id',$option_id)->where('visitor_id',Session::get('visitor_id'))->get();
            if(count($is_exist)>0)
            {
                $select_count = $is_exist[0]->select_count+1;
                if($select_count<0)
                {
                    $select_count = 0;
                }
                ActivityResponse::where('activity_id',$activity_id)->where('sub_activity_id',$sub_activity_id)->where('option_id',$option_id)->where('visitor_id',Session::get('visitor_id'))->update(array("select_count" => $select_count,"response_from" => $polling_url));
            }
            else
            {
                $insert_array = array(

                    "activity_id"       => $activity_id,
                    "sub_activity_id"   => $sub_activity_id,
                    "visitor_id"        => Session::get('visitor_id'),
                    "option_id"         => $option_id,
                    "select_count"      => 1,
                    "response_from"     => $polling_url,
                    "unique_name"       => Session::get('unique_name'),
                    "ip_address"        => $ip,

                );
                ActivityResponse::create($insert_array);
            }
        }
        else
        {
            $options_data   = ActivityOption::find($option_id);
            $select_count   = $options_data->select_count+1;
            if($select_count<0)
            {
                $select_count = 0;
            }
            $options_data->select_count = $select_count;
            $options_data->save();

            $is_exist = ActivityResponse::select('select_count')->where('activity_id',$activity_id)->where('visitor_id',Session::get('visitor_id'))->where('option_id',$option_id)->get();
            if(count($is_exist)>0)
            {
                $select_count = $is_exist[0]->select_count+1;
                if($select_count<0)
                {
                    $select_count = 0;
                }
                ActivityResponse::where('activity_id',$activity_id)->where('visitor_id',Session::get('visitor_id'))->where('option_id',$option_id)->update(array("select_count" => $select_count,"response_from" => $polling_url));
            }
            else
            {
                $insert_array = array(

                    "activity_id"       => $activity_id,
                    "visitor_id"        => Session::get('visitor_id'),
                    "option_id"         => $option_id,
                    "select_count"      => 1,
                    "response_from"     => $polling_url,
                    "unique_name"       => Session::get('unique_name'),
                    "ip_address"        => $ip,

                );
                ActivityResponse::create($insert_array);
            }
        }
        return true;
    }

    # Remove selected options

    public function remove_selected_option($activity_id="",$sub_activity_id="",$option_id="")
    {
        $userdata       = User::first();
        $polling_url    = "";
        if($userdata->polling_url)
        {
            $polling_url = $userdata->polling_url;
        }

        $activity_data      = Activity::find($activity_id);
        $select_count       = $activity_data->total_responses-1;
        if($select_count<0)
        {
            $select_count = 0;
        }
        $activity_data->total_responses = $select_count;
        $activity_data->save();

        if($sub_activity_id !="")
        {
            $sub_activity_data      = ActivityMultiple::where("activity_id",$activity_id)->where("id",$sub_activity_id)->first();
            $select_count   = $sub_activity_data->select_count-1;
            if($select_count<0)
            {
                $select_count = 0;
            }
            $sub_activity_data->select_count = $select_count;
            $sub_activity_data->save();

            $sub_activity_data      = ActivityOption::where("activity_id",$activity_id)->where("sub_activity_id",$sub_activity_id)->where("id",$option_id)->first();
            $select_count   = $sub_activity_data->select_count-1;
            if($select_count<0)
            {
                $select_count = 0;
            }
            $sub_activity_data->select_count = $select_count;
            $sub_activity_data->save();

            $is_exist = ActivityResponse::select('select_count')->where('activity_id',$activity_id)->where('visitor_id',Session::get('visitor_id'))->where('option_id',$option_id)->get();
            if(count($is_exist)>0)
            {
                $select_count = $is_exist[0]->select_count-1;
                if($select_count<0)
                {
                    $select_count = 0;
                }
                ActivityResponse::where('activity_id',$activity_id)->where('visitor_id',Session::get('visitor_id'))->where('option_id',$option_id)->update(array("select_count" => $select_count,"response_from" => $polling_url));
            }
        }
        else
        {
            $options_data   = ActivityOption::find($option_id);
            $select_count   = $options_data->select_count-1;
            if($select_count<0)
            {
                $select_count = 0;
            }
            $options_data->select_count = $select_count;
            $options_data->save();
           
            $is_exist = ActivityResponse::select('select_count')->where('activity_id',$activity_id)->where('visitor_id',Session::get('visitor_id'))->where('option_id',$option_id)->get();
            if(count($is_exist)>0)
            {
                $select_count = $is_exist[0]->select_count-1;
                if($select_count<0)
                {
                    $select_count = 0;
                }
                ActivityResponse::where('activity_id',$activity_id)->where('visitor_id',Session::get('visitor_id'))->where('option_id',$option_id)->update(array("select_count" => $select_count,"response_from" => $polling_url));
            }
        }

        return true;
    }

    # Save visitors responses

    public function save_visitors_response(Request $request)
    {
        $activity_id        = app_decode($request->activity_id);
        $added_response     = array();
        $removed_response   = array();
        if(isset($request->added_response))
        {
            $added_response     = $request->added_response;
        }
        if(isset($request->removed_response))
        {
            $removed_response   = $request->removed_response;
        }
        for ($i=0; $i < count($added_response); $i++) 
        { 
            $option_id       = $added_response[$i];
            $this->add_selected_option($activity_id,"",$option_id,$request->ip());
        }
        for ($i=0; $i < count($removed_response); $i++) 
        { 
            $option_id       = $removed_response[$i];
            $this->remove_selected_option($activity_id,"",$option_id,$request->ip());
        }
        return response()->json(array("message" => "Cool"),200);
    }

    public function save_visitors_response_multiple(Request $request)
    {
        $activity_id        = app_decode($request->activity_id);
        $added_response     = array();
        $removed_response   = array();
        if(isset($request->added_response))
        {
            $added_response     = $request->added_response;
        }
        if(isset($request->removed_response))
        {
            $removed_response   = $request->removed_response;
        }

        for ($i=0; $i < count($added_response); $i++) 
        { 
            $sub_activity_id    = app_decode($added_response[$i]['question_id']);
            $option_id          = $added_response[$i]['option_id'];
            $this->add_selected_option($activity_id,$sub_activity_id,$option_id,$request->ip());
        }

        for ($i=0; $i < count($removed_response); $i++) 
        {
            $sub_activity_id    = app_decode($removed_response[$i]['question_id']);
            $option_id          = $removed_response[$i]['option_id'];
            $this->remove_selected_option($activity_id,$sub_activity_id,$option_id,$request->ip());
        }
        return response()->json(array("message" => "Cool"),200);
    }

    public function receive_sms(Request $request){
        $values = array('data' => json_encode($request->all()));
        DB::table('sms_data')->insert($values);

        $response = new MessagingResponse();
        $response->message("Thanks for contact us. checking reply sms");
        print_r($response);exit;
    }

    public function receive_sms1(Request $request){
        $values = array('data' => json_encode($request->all()));
        $client = new TwilioClient(Cache::get('TWILIO_SECRET'), Cache::get('TWILIO_TOKEN'));
        //$client->messages->create("+14088230500", [
        $client->messages->create("+14088230500", [
            'from' => Cache::get('TWILIO_FROM'),
            'body' => "Can you sent message again to this number for testing purpose"
        ]);
        print_r($client);exit;
    }

    public function get_visitor_request(Request $request)
    {
        $voted_option           = array();
        $invalid_option         = array();
        $count_exceed_option    = array();
        $message                = array();

        $twillio_from   = Cache::get('TWILIO_FROM');
        $responseData   = json_encode($request->all());
        $bodyData       = $request->all();
        $values         = array('data' => $responseData);

        DB::table('sms_data')->insert($values);
        $temp = array();
        foreach ($bodyData as $key => $value) {
            $temp[$key] = rtrim($value,',');
        }

        $bodyData       = $temp;
        $is_new_visitor = 0;
        if(isset($bodyData['Body']))
        {
            if(isset($bodyData['From']))
            {
                $activity_id    = "";
                $is_final       = 0;
                $phone_number   = trim($bodyData['From']);
                if($bodyData['Body'] !="")
                {
                    $VisitorsData = Visitors::where('phone_number',$phone_number)->get();
                    if(count($VisitorsData)==0)
                    {
                        $unique_name    = $this->get_guest_name();
                        $save_data = array(
                            "phone_number"      => $phone_number,
                            "unique_name"       => $unique_name,
                            "ip_address"        => $request->ip(),
                        );
                        $visitor_id     = Visitors::insertGetId($save_data);
                        $is_new_visitor++;
                    }
                    else
                    {
                        $unique_name = $VisitorsData[0]->unique_name;
                        $visitor_id  = $VisitorsData[0]->id;
                    }
                    Session::put('unique_name',$unique_name);
                    Session::put('visitor_id',$visitor_id);
                    if($visitor_id)
                    {
                        $activity_data  = Activity::where('status',1)->get();
                        $text           = trim($bodyData['Body']);
                        $UserCount      = User::where('username',$text)->count();
                        if($UserCount>0)
                        {
                            if(count($activity_data)>0)
                            {
                                $response_type = $activity_data[0]->response_type;
                                $array = explode(',', $response_type);
                                if(in_array(2, $array))
                                {
                                    // $activity_name  = $activity_data[0]->title;
                                    // $message[]      = $activity_name;

                                    // $optionsData    = ActivityOption::where('activity_id',$activity_data[0]->id)->orderBy('sort_order','asc')->get();
                                    // foreach ($optionsData as $value)
                                    // {
                                    //     $message[]  = $value->option_name.") ".$value->option;
                                    // }
                                    // if($is_new_visitor>0)
                                    // {
                                    //     $message[]  = "Please send a reply anyone of these option to ".$twillio_from;
                                    // }
                                    $message[] = "You have joined the ".$text." poll and can start voting now.";
                                }
                                else
                                {
                                    $message[] = "Text message option is currently not available.";
                                }
                            }
                            else
                            {
                                $message[] = "No activated activity.";
                            }
                        }
                        else
                        {
                            $optionText = explode(',', $text);
                            for ($i=0; $i < count($optionText); $i++) 
                            { 
                                $text           = trim($optionText[$i]);
                                $optionsData    = ActivityOption::where('activity_id',$activity_data[0]->id)->where('option_name',$text)->where('option_name','!=','')->get();
                                if(count($optionsData)>0)
                                {
                                    $may_respond_count      = 0;
                                    $may_select_count       = 0;
                                    if(count($activity_data)>0)
                                    {
                                        if(isset($activity_data[0]))
                                        {
                                          $may_respond_count   = ($activity_data[0]->may_respond_count=='')? -1 :$activity_data[0]->may_respond_count;
                                          $may_select_count    = ($activity_data[0]->may_select_count=='')? -1 :$activity_data[0]->may_select_count;
                                        }

                                        $response_type = $activity_data[0]->response_type;
                                        $array = explode(',', $response_type);
                                        if(in_array(2, $array))
                                        {
                                            if($may_respond_count==config('constant.unlimited_value'))
                                            {
                                              $may_respond_count = -1;
                                            }
                                            if($may_select_count==config('constant.unlimited_value'))
                                            {
                                              $may_select_count = -1;
                                            }
                                            
                                            if($may_respond_count== -1)
                                            {
                                              $may_respond_count = config('constant.unlimited_value');
                                            }
                                            if($may_select_count== -1)
                                            {
                                              $may_select_count = config('constant.unlimited_value');
                                            }

                                            $Response = ActivityResponse::select(DB::raw('SUM(select_count) as total_select'))->where('activity_id',$activity_data[0]->id)->where('visitor_id',$visitor_id)->first();
                                            $current_total = $Response->total_select;

                                            $ResponseData = ActivityResponse::select('select_count')->where('activity_id',$activity_data[0]->id)->where('visitor_id',$visitor_id)->where('option_id',$optionsData[0]->id)->get();
                                            
                                            $current_indivitual = 0;
                                            if(count($ResponseData)>0)
                                            {
                                                $current_indivitual = $ResponseData[0]->select_count;
                                            }

                                            if($current_total < $may_respond_count)
                                            {
                                                if($current_indivitual < $may_select_count)
                                                {
                                                    $this->save_selected_options($activity_data[0]->id,$optionsData[0]->id,$twillio_from);
                                                    $activity_id = app_encode($activity_data[0]->id);
                                                    $voted_option[] = $text;
                                                    $is_final       = 1;
                                                }
                                                else
                                                {
                                                    $count_exceed_option[] = $text;
                                                }
                                            }
                                            else
                                            {
                                                $message[] = "You have reached maximum allowed count.";
                                            }
                                        }
                                        else
                                        {
                                           $message[] = "Text message option is currently not available.";
                                        }
                                    }
                                    else
                                    {
                                        $message[] = "No activated activity.";
                                    }
                                }
                                else
                                {
                                    $invalid_option[] = $text;
                                }
                            }
                        }
                    }
                    else
                    {
                        $message[] = "Unable to find your account.";
                    }
                }
                else
                {
                    $message[] = "Plesae provide valid response code or option.";
                }

                $message                = array_unique($message);
                $voted_option           = $voted_option;
                $invalid_option         = array_unique($invalid_option);
                $count_exceed_option    = array_unique($count_exceed_option);

                $response = "";
                
                if(count($voted_option)>0)
                {
                    $response .= "You have entered option : ".implode(',', $voted_option);    
                }
                
                if(count($count_exceed_option)>0)
                {
                    $response .= "\nYou have reached maximum allowed count for : ".implode(',', $count_exceed_option);    
                }
                foreach ($message as $value) 
                {
                    $response .= "\n".$value;
                }
                if(count($invalid_option)>0)
                {
                    $response .= "\nYou have entered invalid response code or option : ".implode(',', $invalid_option);
                }
                if($response !="")
                {
                    return $this->send_twillio_sms($response,$phone_number,$is_final,$activity_id);
                }
            }
        }
    }

    public function save_selected_options($activity_id="",$option_id="",$response_from="")
    {
        $activity_data   = Activity::find($activity_id);
        $activity_data->total_responses += 1;
        $activity_data->save();

        $options_data   = ActivityOption::find($option_id);
        $options_data->select_count += 1;
        $options_data->save();

        $is_exist = ActivityResponse::select('select_count')->where('activity_id',$activity_id)->where('visitor_id',Session::get('visitor_id'))->where('option_id',$option_id)->get();
        if(count($is_exist)>0)
        {
            $select_count = $is_exist[0]->select_count+1;
            ActivityResponse::where('activity_id',$activity_id)->where('visitor_id',Session::get('visitor_id'))->where('option_id',$option_id)->update(array("select_count" => $select_count,"response_from" => $response_from));
        }
        else
        {
            $insert_array = array(

                "activity_id"       => $activity_id,
                "visitor_id"        => Session::get('visitor_id'),
                "option_id"         => $option_id,
                "select_count"      => 1,
                "response_from"     => $response_from,
                "unique_name"       => Session::get('unique_name'),

            );
            ActivityResponse::create($insert_array);
        }
    }

    public function send_twillio_sms($message="",$to_number="",$is_final=0,$activity_id="")
    {
        $twillio_from   = Cache::get('TWILIO_FROM');
        $twillio_sid    = Cache::get('TWILIO_SECRET');
        $twillio_token  = Cache::get('TWILIO_TOKEN');
        try {
            $client = new TwilioClient($twillio_sid, $twillio_token);
            if($to_number!="" && $message!="")
            {
                $client->messages->create($to_number, [
                    'from' => $twillio_from,
                    'body' => $message
                ]);
            }
            if($is_final==1 && $activity_id !="")
            {
                $activity_id = app_decode($activity_id);
                $this->send_websockets($activity_id);
            }
            else
            {
                return true;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    # Push Notification
    public function send_websockets($activity_id)
    {
        $activity_data = Activity::select('created_by')->where('id',$activity_id)->get();
        if(count($activity_data)>0)
        {
            $admin_id = $activity_data[0]->created_by;
            $data = json_encode(['type'=>'get-poll-result','activity_id'=>app_encode($activity_id)]);
            $client = new Client(config('app.socket_url').'?user_type=visitor&user_id='.Session::get('visitor_id'));
            $res = $client->text($data);
            return response()->json(['success' => 'OK'], 200);
        }
        else
        {
            return response()->json(['success' => 'FAILED'], 400);
        }
    }

    public function thank_you()
    {
        return view('visitors.thank_you');
    }
}
