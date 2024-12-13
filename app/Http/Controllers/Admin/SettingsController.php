<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\ActivitySettings;
use App\Models\GeneralSettings;
use Auth;
use Hash;
use Validator;
use Cache;

class SettingsController extends Controller
{
    # Activity settings page
    public function activity_settings(Request $request)
    {
        $edit_data = ActivitySettings::first();
        return view('admin.settings.activity_settings',compact('edit_data'));
    }

    # Save activity settings
    public function save_activity_settings(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'amount_per_score'              => 'required',
            'is_visitor_change_answer'      => 'required',
            'is_text_message'               => 'required',

        ]);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors());
        }
        try
        {
            $save_Data = array(
                "amount_per_score"              => $request->amount_per_score,
                "is_visitor_change_answer"      => $request->is_visitor_change_answer,
                "is_text_message"               => $request->is_text_message,
                "ip_address"                    => $request->ip(),
                "id"                            => $request->id,
            );
            ActivitySettings::updateOrCreate(['id' =>$request->id],$save_Data);
            return redirect()->route('activity-settings')->with('success',trans('admin.activity_settings_saved'));
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    # General settings page
    public function general_settings(Request $request)
    {
        $edit_data = GeneralSettings::first();
        return view('admin.settings.general_settings',compact('edit_data'));
    }

    # Save activity settings
    public function save_general_settings(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'smtp_mailer'               => 'required',
            'smtp_host'                 => 'required',
            'smtp_port'                 => 'required',
            'smtp_username'             => 'required',
            'smtp_password'             => 'required',
            'smtp_encryption'           => 'required',
            'twilio_secret_key'         => 'required',
            'twilio_token'              => 'required',
            'twilio_from_code'          => 'required',
            'twilio_from_number'        => 'required',

        ]);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors());
        }
        try
        {
            $save_Data = array(

                "smtp_mailer"               => $request->smtp_mailer,
                "smtp_host"                 => $request->smtp_host,
                "smtp_port"                 => $request->smtp_port,
                "smtp_username"             => $request->smtp_username,
                "smtp_password"             => $request->smtp_password,
                "smtp_encryption"           => $request->smtp_encryption,
                "twilio_secret_key"         => $request->twilio_secret_key,
                "twilio_token"              => $request->twilio_token,
                "twilio_from_code"          => $request->twilio_from_code,
                "twilio_from_number"        => $request->twilio_from_number,
                "ip_address"                => $request->ip(),
                "id"                        => $request->id,
            );

            GeneralSettings::updateOrCreate(['id' =>$request->id],$save_Data);
            Cache::forever('MAIL_MAILER',$request->smtp_mailer);
            Cache::forever('MAIL_HOST',$request->smtp_host);
            Cache::forever('MAIL_PORT',$request->smtp_port);
            Cache::forever('MAIL_USERNAME',$request->smtp_username);
            Cache::forever('MAIL_PASSWORD',$request->smtp_password);
            Cache::forever('MAIL_ENCRYPTION',$request->smtp_encryption);
            Cache::forever('MAIL_FROM_ADDRESS',$request->smtp_username);
            Cache::forever('MAIL_FROM_NAME',env('APP_NAME'));
            Cache::forever('TWILIO_FROM_CODE',$request->twilio_from_code);
            Cache::forever('TWILIO_FROM_NUMBER',$request->twilio_from_number);
            Cache::forever('TWILIO_FROM','+'.$request->twilio_from_code.$request->twilio_from_number);
            Cache::forever('TWILIO_SECRET',$request->twilio_secret_key);
            Cache::forever('TWILIO_TOKEN',$request->twilio_token);

            return redirect()->route('general-settings')->with('success',trans('admin.general_settings_saved'));
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
