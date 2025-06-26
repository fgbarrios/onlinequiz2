<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitors;
use App\Models\Activity;
use App\Models\ActivityOption;
use App\Models\TimeZones;
use App\Models\InviteVisitors;
use App\Models\AdminLogs;
use App\Mail\CustomSendMail;
use Auth;
use Hash;
use Mail;
use Session;
use Validator;
use DateTimeZone;
use DateTime;
use Cache;

class HomeController extends Controller
{
    # Dashboard or login page
    public function index(Request $request)
    {
        if(!auth()->check())
        {
            return redirect('login');
        }
        $remember_token = auth()->user()->remember_token;
        $AdminLogs = AdminLogs::where('admin_id',Auth::user()->id)->orderBy('id','desc')->get();
        $old_token = "";
        if(count($AdminLogs)>0)
        {
            $old_token = $AdminLogs[0]->remember_token;
        }
        if($old_token != $remember_token)
        {
            $Insertdata = [
                "admin_id"      => auth()->user()->id,
                "browser_agent" => $request->header('user-agent'),
                "remember_token" => $remember_token,
                "ip_address"    => $request->ip(),
                "logged_at"     => now(),
            ];
            AdminLogs::create($Insertdata);
        }
        return redirect('dashboard');
    }

    # Change local language
    public function change_language($lang)
    {
        if($lang)
        {
            App::setLocale($lang);
            session()->put('locale',$lang);
        }
        return redirect()->back();
    }

    # Dashoard page
    public function dashboard(Request $request)
    {
        $activity_data  = Activity::orderBy('updated_at','desc')->limit(10)->get();
        $active_count   = Activity::where('status' , 1)->count();
        return view('admin.dashboard',compact('activity_data','active_count'));
    }

    # Invite Visitors
    public function invite_visitors(Request $request)
    {
        return view('admin.invite_visitors');
    }

    # Invite Visitors through email
    public function invite_visitors_via_email(Request $request)
    {
        $validatedData = $request->validate([
        // 'message'       => 'required|min:10|max:2000',
        'email'         => [
            'required',
            function ($attribute, $value, $fail) {
                $emails = array_map('trim', explode(',', $value));
                $validator = Validator::make(['emails' => $emails], ['emails.*' => 'required|email']);
                if ($validator->fails()) {
                $fail('All email addresses must be valid.');
                }
            },
        ],
        ], [
            'message.required' => 'Please provide valid message.',
            'message.min' => 'Message must be greater than or equal to 10 characters.',
            'message.max' => 'Message must be less than or equal to 2000 characters.',
        ]);

        $emails     = $request->email;
        $message    = $request->message;

        $data = array(
            "emails"    => $emails,
            "message"   => $message,
        );

        InviteVisitors::create($data);
        $email_array = explode(',', $emails);
        foreach ($email_array as $email) 
        {
            $mailContent = array(
                "email"         => $email,
                "message"       => $message,
                "visit_url"     => auth()->user()->polling_url,
            );

            $mailData = [
                'subject'   => 'Invite Visitors',
                'view'      => 'admin.email.invite_visitors',
                'data'      => $mailContent,
            ];
            // return view('admin.email.invite_visitors',compact('mailData'));
            try {

                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\CustomSendMail($mailData));

            } catch (\Exception $e) {

                return redirect()->back()->withErrors($e->getMessage());
            }
        }

        return redirect()->back()->with('success', trans('admin.invite_success'));
    }

    # Profile page
    public function profile()
    {
        $timezones = TimeZones::all();
        return view('admin.profile',compact('timezones'));
    }

    # Update user profile
    public function update_profile(Request $request)
    {
        $validatedData = $request->validate([
            'username'      => 'required|min:3|max:100',
            'polling_url'   => 'required|string',
            'email'         => 'required|string|email|max:250',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'max:100',
            'phone_number'  => 'required',
            'time_zone'     => 'required',
        ], [
            'username.required' => 'Please provide valid username.',
            'username.min' => 'Username must be greater than or equal to 4 characters.',
            'username.max' => 'Username must be less than or equal to 100 characters.',
            'polling_url.required' => 'Please provide valid polling URL.',
            'email.required' => 'Please provide valid email.',
            'first_name.required' => 'Please provide valid first name.',
            'first_name.max' => 'First name must be less than or equal to 100 characters.',
            'last_name.max' => 'Last name must be less than or equal to 100 characters.',
            'phone_number.required' => 'Please provide valid phone number.',
            'time_zone.required' => 'Please provide valid time zone.',
        ]);

        $update_data = array(
            "username"      => $request->username,
            "polling_url"   => $request->polling_url,
            "email"         => $request->email,
            "first_name"    => $request->first_name,
            "last_name"     => $request->last_name,
            "phone_code"    => $request->phone_code,
            "phone_number"  => $request->phone_number,
            "time_zone"     => $request->time_zone,
        );
        
        if ($request->filled('password')) {
            $update_data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $result = \App\Models\User::where('id', \Illuminate\Support\Facades\Auth::id())->update($update_data);
        $timeData = TimeZones::where('id', $request->time_zone)->first();
        if (isset($timeData->zone_text)) {
            \Illuminate\Support\Facades\Cache::forever('APP_TIMEZONE', $timeData->zone_text);
            date_default_timezone_set(\Illuminate\Support\Facades\Cache::get('APP_TIMEZONE'));
        }
        return redirect()->back()->with('success', trans('admin.profile_update'));
    }

    # Login page
    public function login(Request $request)
    {
        return view('admin.login');
    }

    # Forget password page
    public function forget_password(Request $request)
    {
        return view('admin.forget_password');
    }

    # Change password page
    public function change_password()
    {
        return view('admin.change_password');
    }

    # Visti page
    public function visit($username = "")
    {
        if (\Illuminate\Support\Facades\Session::get('unique_name')) {
            return redirect()->route('vote-page');
        } else
        {
            $userdata   = User::where('username',$username)->get();
            $name       = "";
            if(count($userdata)>0)
            {
                $username = $userdata[0]->username;
                return view('visitors.details',compact('name','username'));
            }
            else
            {
                return redirect('/');
            }
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

        if(!\Illuminate\Support\Facades\Session::get('unique_name'))
        {
            $unique_name = $this->get_guest_name();
            \Illuminate\Support\Facades\Session::put('unique_name', $unique_name);
            $save_data = array(
            
                "unique_name"       => $unique_name,
                "ip_address"        => $request->ip(),

            );
            Visitors::create($save_data);
        }

        $name = \Illuminate\Support\Facades\Session::get('unique_name');
        $activity_data = Activity::where('status', '1')->orderBy('sort_order', 'asc')->get();

        if ($activity_data->count() > 0)
        {
            $activity_data  = $activity_data[0];
            $option_data    = ActivityOption::where('activity_id',$activity_data->id)->orderBy('sort_order','asc')->get();
            return view('visitors.poll',compact('name','activity_data','option_data','username'));
        }
        else
        {
            return view('visitors.presentation',compact('name','username'));
        }
    }

    # Save visitors details
    public function save_visitors_details(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name'          => 'required|string',
            // 'email'         => 'email',
            // 'phone_number'  => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10'
        ]);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors());
        }

        $unique_name = $request->name;

        $save_data = array(

            "name"              => $request->name,
            "email"             => $request->email,
            "phone_number"      => $request->phone_number,
            "unique_name"       => $unique_name,
            "ip_address"        => $request->ip(),

        );
        Visitors::create($save_data);
        \Illuminate\Support\Facades\Session::put('unique_name', $unique_name);
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

    # Change new password
    public function change_new_password(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'old_password'          => 'required|string',
            'new_password'          => 'required|string',
            'confirm_password'      => 'required|same:new_password',
        ]);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors());
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->old_password, \Illuminate\Support\Facades\Auth::user()->password)) {
            return redirect()->back()->withErrors([trans('admin.old_password_not_matched')]);
        }

        \App\Models\User::where('id', \Illuminate\Support\Facades\Auth::user()->id)
            ->update(['password' => \Illuminate\Support\Facades\Hash::make($request->new_password)]);
        return redirect()->back()->with('success',trans('admin.password_changed'));
    }

    // Save All Timzones

    public function save_timezone(Request $request)
    {
        $allTimezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        foreach ($allTimezones as $timezone) 
        {
            $dateTimeZone = new DateTimeZone($timezone);
            $currentTime = new DateTime('now', $dateTimeZone);

            $timezoneName = $dateTimeZone->getName();
            $timezoneOffset = $currentTime->format('P');

            $save = array(
                "zone_text" => $timezoneName,
                "zone_value" => $timezoneOffset,
            );
            TimeZones::create($save);
        }
        echo "Done";
    }
}
