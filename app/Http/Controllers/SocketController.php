<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\Visitors;
use App\Models\Activity;
use App\Models\ActivityMultiple;
use App\Models\ActivityResponse;
use App\Models\ActivityOption;
use App\Models\User;
use App\Models\Socket;
use App\Models\ActivitySettings;
use Session;
use Auth;
use DB;
 
class SocketController extends Controller implements MessageComponentInterface
{
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }
 
    public function onOpen(ConnectionInterface $conn) 
    {
        ini_set("memory_limit", "-1");
        $this->clients->attach($conn);
        $querystring = $conn->httpRequest->getUri()->getQuery();
        parse_str($querystring, $queryarray);
        if($queryarray['user_type']=="admin")
        {
            $data['type']           = 'admin';
            $data['resource_id']    = $conn->resourceId;
            $data['unique_id']      = $queryarray['user_id'];
            Socket::create($data);

            // $data = array("action" => "Open","client_id" => $conn->resourceId,"user_type" => "admin","unique_id" => $queryarray['user_id'],"message" => "Connection Opened");
            // echo "\nlog_date : ".date('Y-m-d H:i:s')." ".json_encode($data);
        }
        else
        {
            $data['type']           = 'visitors';
            $data['resource_id']    = $conn->resourceId;
            $data['unique_id']      = $queryarray['user_id'];
            Socket::create($data);
            
            // $data = array("action" => "Open","client_id" => $conn->resourceId,"user_type" => "visitors","unique_id" => Session::get('visitor_id'),"message" => "Connection Opened");
            // echo "\nlog_date : ".date('Y-m-d H:i:s')." ".json_encode($data);

        }
        $result = json_encode(array("type" => "id","data" => $conn->resourceId));
        $conn->send($result);
    }

    public function onMessage(ConnectionInterface $from, $msg) 
    {
        ini_set("memory_limit", "-1");
        $html           = "";
        $data           = json_decode($msg);
        $userdata       = User::first();
        $username       = "";

        if($userdata->username)
        {
            $username = $userdata->username;
        }

        if($data)
        {
            $socket_data           = Socket::select('resource_id')->where('type','admin')->get();
            $admin_resource_ids    = array();
            foreach ($socket_data as $value)
            {
                $admin_resource_ids[] = $value['resource_id'];
            }

            if($data->type =="poll-check-state")
            {
                $settingsData          = ActivitySettings::select('is_visitor_change_answer')->get();
                $is_changable          = 2;
                if(count($settingsData)>0)
                {
                    $is_changable = $settingsData[0]->is_visitor_change_answer;
                }

                $activity_data  = Activity::where('status','1')->orderBy('sort_order','asc')->get();
                if(count($activity_data)>0)
                {
                    $activity_data  = $activity_data[0];
                    $mysql = Socket::select('resource_id','unique_id')->where('type','visitors');
                    if(Session::get('visitor_id'))
                    {
                        $mysql->where('unique_id',Session::get('visitor_id'));
                    }
                    $socketData = $mysql->get();
                    foreach ($socketData as $vs_value) 
                    {
                        if($activity_data->is_multiple_type==1)
                        {
                            $OptionArray      = array();
                            $QuestionData   = ActivityMultiple::where('activity_id',$activity_data->id)->orderBy('sort_order','asc')->get();

                            foreach ($QuestionData as $value_q) 
                            {
                                $MainArray['question']  = $value_q->question;
                                $total_count            = 1;

                                $responseData   = ActivityResponse::select(DB::raw('SUM(select_count) as select_count'))->where('activity_id',$activity_data->id)->where('sub_activity_id',$value_q->id)->where('visitor_id',$vs_value->unique_id)->get();
                                $select_count   = 0;
                                if(count($responseData)>0)
                                {
                                    $select_count = $responseData[0]->select_count;
                                }

                                $available_count                = ($total_count-$select_count);

                                $MainArray['question_id']       = $value_q->id;
                                $MainArray['select_count']      = $select_count;
                                $MainArray['total_count']       = $total_count;
                                $MainArray['available_count']   = $available_count;

                                $optionData         = ActivityOption::where('activity_id',$activity_data->id)->where('sub_activity_id',$value_q->id)->orderBy('sort_order','asc')->get();
                                $optionArray        = array();

                                foreach ($optionData as $value_p) 
                                { 
                                    $responseData   = ActivityResponse::select(DB::raw('SUM(select_count) as select_count'))->where('activity_id',$activity_data->id)->where('sub_activity_id',$value_q->id)->where('option_id',$value_p->id)->where('visitor_id',$vs_value->unique_id)->get();
                                    $select_count   = 0;
                                    if(count($responseData)>0)
                                    {
                                        $select_count = $responseData[0]->select_count;
                                    }

                                    $tempOption['id']               = $value_p->id;
                                    $tempOption['option']           = $value_p->option;
                                    $tempOption['score']            = $value_p->score;
                                    $tempOption['select_count']     = $select_count;
                                    $optionArray[]                  = $tempOption;
                                }

                                $MainArray['options']   = $optionArray;
                                $OptionArray[]          = $MainArray;
                            }

                            $option_data    = $OptionArray;
                            $html           = view('visitors.poll_multi',compact('activity_data','option_data','username','is_changable'));
                            foreach ( $this->clients as $client) 
                            {
                                if($client->resourceId==$vs_value->resource_id)
                                {
                                    $client->send($html);
                                    // aa($option_data);
                                    // $data = array("action" => "Message","client_id" => $client->resourceId,"user_type" => "visitors","unique_id" => $vs_value->unique_id,"message" => "poll-check-state");
                                    // echo "\nlog_date : ".date('Y-m-d H:i:s')." ".json_encode($data);
                                }
                            }
                        }
                        else
                        {
                            $optionData     = ActivityOption::select('id','option','option_image')->where('activity_id',$activity_data->id)->orderBy('sort_order','asc')->get();
                            $total_count    = 0;
                            foreach ($optionData as $value) 
                            {
                                $responseData   = ActivityResponse::select('select_count')->where('activity_id',$activity_data->id)->where('option_id',$value->id)->where('visitor_id',$vs_value->unique_id)->get();
                                $total_select   = 0;
                                if(count($responseData)>0)
                                {
                                    $total_select = $responseData[0]->select_count;
                                }

                                $total_count                += $total_select;

                                $temp = [];
                                $temp['may_select_count']   = $activity_data->may_select_count;
                                $temp['available_select']   = ($activity_data->may_select_count-$total_select);
                                $temp['total_select']       = $total_select;
                                $temp['option_id']          = $value->id;
                                $temp['option_text']        = $value->option;
                                $temp['option_image']       = $value->option_image;
                                $temp['activity_id']        = $activity_data->id;
                                $temp['visitor_id']         = $vs_value->unique_id;

                                $response_data['option_id_'.$value->id] = $temp;

                                $activity_data->total_count = $total_count;

                                $option_data    = ActivityOption::where('activity_id',$activity_data->id)->orderBy('sort_order','asc')->get();
                                $html = view('visitors.poll',compact('activity_data','option_data','response_data','username','is_changable'));
                                foreach ( $this->clients as $client) 
                                {
                                    if($client->resourceId==$vs_value->resource_id)
                                    {
                                        $client->send($html);
                                        // $data = array("action" => "Message","client_id" => $client->resourceId,"user_type" => "visitors","unique_id" => $vs_value->unique_id,"message" => "poll-check-state");
                                        // echo "\nlog_date : ".date('Y-m-d H:i:s')." ".json_encode($data);
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    $html   = view('visitors.presentation',compact('username'));
                    foreach ( $this->clients as $client) 
                    {
                        if(!in_array($client->resourceId, $admin_resource_ids))
                        {
                            $client->send($html);
                            // $data = array("action" => "Message","client_id" => $client->resourceId,"user_type" => "visitors","unique_id" => Session::get('visitor_id'),"message" => "poll-check-state");
                            // echo "\nlog_date : ".date('Y-m-d H:i:s')." ".json_encode($data);
                        }
                    }
                }
            }
            else if($data->type=="get-poll-result")
            {
                $id             = app_decode($data->activity_id);
                $activity_data  = Activity::where('id',$id)->get();
                if(count($activity_data)>0)
                {
                    if($activity_data[0]->is_multiple_type==1)
                    {
                        $QuestionArray  = array();
                        $sub_activity_data = ActivityMultiple::where('activity_id',$activity_data[0]->id)->orderBy('sort_order','asc')->get();
                        foreach ($sub_activity_data as $value) 
                        {
                            $tempArray                  = array();
                            $tempArray['question_id']   = $value->id;
                            $tempArray['question']      = $value->question;
                            $tempArray['select_count']  = $value->select_count;

                            $option_data    = ActivityOption::where('activity_id',$id)->where('sub_activity_id',$value->id)->orderBy('sort_order','asc')->get();
                            $subArray = array();
                            foreach ($option_data as $value1) 
                            {
                                $temp                   = array();
                                $temp['id']             = $value1->id;
                                $temp['is_correct']     = $value1->is_correct;
                                $temp['score']          = $value1->score;
                                $temp['option']         = $value1->option;
                                $temp['select_count']   = $value1->select_count;
                                $subArray[]             = $temp;
                            }
                            $tempArray['options']       = $subArray;
                            $QuestionArray[]            = $tempArray;
                        }
                        $result = json_encode(array("type" => "data","data" => array("activity_data" => $activity_data,"question_array" => $QuestionArray)));
                    }
                    else
                    {
                        $option_data    = ActivityOption::where('activity_id',$id)->orderBy('sort_order','asc')->get();
                        $result = json_encode(array("type" => "data","data" => array("activity_data" => $activity_data,"option_data" => $option_data)));
                    }
                    
                    if(isset($data->resID))
                    {
                        foreach ( $this->clients as $client) 
                        {
                            if($client->resourceId==$data->resID)
                            {
                                if($data->adminid !="")
                                {
                                    $savedata                   = array();
                                    $savedata['type']           = 'admin';
                                    $savedata['resource_id']    = $client->resourceId;
                                    $savedata['unique_id']      = $data->adminid;
                                    $savedata['activity_id']    = $id;
                                    Socket::create($savedata);
                                }
                                $client->send($result);
                                // $data = array("action" => "Message","client_id" => $client->resourceId,"user_type" => "admin","unique_id" => $data->adminid,"message" => "get-poll-result");
                                // echo "\nlog_date : ".date('Y-m-d H:i:s')." ".json_encode($data);
                            }
                        }
                    }
                    else
                    {
                        $Data = Socket::select('resource_id')->where('type','admin')->where('activity_id',$id)->get();
                        $adminids = array();
                        foreach ($Data as $value11) 
                        {
                            $adminids[] = $value11->resource_id;
                        }
                        foreach ( $this->clients as $client) 
                        {
                            if(in_array($client->resourceId, $adminids))
                            {
                                $client->send($result);
                                // $data = array("action" => "Message","client_id" => $client->resourceId,"user_type" => "admin","unique_id" => $data->adminid,"message" => "get-poll-result");
                                // echo "\nlog_date : ".date('Y-m-d H:i:s')." ".json_encode($data);
                            }
                        }
                    }
                }
            }
            else if($data->type=="thank-you")
            {
                $socket_data           = Socket::select('resource_id')->where('type','visitors')->where('unique_id',$data->visitor_id)->get();
                $visitors_resource_ids    = array();
                foreach ($socket_data as $value)
                {
                    $visitors_resource_ids[] = $value['resource_id'];
                }
                $html   = view('visitors.thank_you');
                foreach ( $this->clients as $client) 
                {
                    if(in_array($client->resourceId, $visitors_resource_ids))
                    {
                        $client->send($html);
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) 
    {
        $this->clients->detach($conn);
        Socket::where('resource_id',$conn->resourceId)->forceDelete();
        $data = array("action" => "close","client_id" => $conn->resourceId);
        // echo "\nlog_date : ".date('Y-m-d H:i:s')." ".json_encode($data);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) 
    {
        $data = array("action" => "error","client_id" => $conn->resourceId,'message' => $e->getMessage());
        echo "\nlog_date : ".date('Y-m-d H:i:s')." ".json_encode($data);
        $conn->close();
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
}