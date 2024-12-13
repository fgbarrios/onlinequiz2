<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\GeneralSettings;
use Cache;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        if(!Cache::has('APP_TIMEZONE'))
        {
            $userdata = User::select('time_zone.zone_text as zone_text')->join('time_zone','users.time_zone','=','time_zone.id')->first();
            if(isset($userdata->zone_text))
            {
                if($userdata->zone_text!="")
                {
                    Cache::forever('APP_TIMEZONE',$userdata->zone_text);
                }
                else
                {
                    Cache::forever('APP_TIMEZONE','UTC');
                }
            }
        }
        date_default_timezone_set(Cache::get('APP_TIMEZONE'));

        if(!Cache::has('MAIL_MAILER'))
        {
            $generalData = GeneralSettings::first();
            if(isset($generalData->id))
            {
                putenv('MAIL_MAILER='.$generalData->smtp_mailer);
                putenv('MAIL_HOST='.$generalData->smtp_host);
                putenv('MAIL_PORT='.$generalData->smtp_port);
                putenv('MAIL_USERNAME='.$generalData->smtp_username);
                putenv('MAIL_PASSWORD='.$generalData->smtp_password);
                putenv('MAIL_ENCRYPTION='.$generalData->smtp_encryption);
                putenv('MAIL_FROM_ADDRESS='.$generalData->smtp_username);
                putenv('MAIL_FROM_NAME='.getenv('APP_NAME'));
                putenv('TWILIO_FROM_CODE='.$generalData->twilio_from_code);
                putenv('TWILIO_FROM_NUMBER='.$generalData->twilio_from_number);
                putenv('TWILIO_FROM='.'+'.$generalData->twilio_from_code.$generalData->twilio_from_number);
                putenv('TWILIO_SECRET='.$generalData->twilio_secret_key);
                putenv('TWILIO_TOKEN='.$generalData->twilio_token);

                Cache::forever('MAIL_MAILER',$generalData->smtp_mailer);
                Cache::forever('MAIL_HOST',$generalData->smtp_host);
                Cache::forever('MAIL_PORT',$generalData->smtp_port);
                Cache::forever('MAIL_USERNAME',$generalData->smtp_username);
                Cache::forever('MAIL_PASSWORD',$generalData->smtp_password);
                Cache::forever('MAIL_ENCRYPTION',$generalData->smtp_encryption);
                Cache::forever('MAIL_FROM_ADDRESS',$generalData->smtp_username);
                Cache::forever('MAIL_FROM_NAME',env('APP_NAME'));
                Cache::forever('TWILIO_FROM_CODE',$generalData->twilio_from_code);
                Cache::forever('TWILIO_FROM_NUMBER',$generalData->twilio_from_number);
                Cache::forever('TWILIO_FROM','+'.$generalData->twilio_from_code.$generalData->twilio_from_number);
                Cache::forever('TWILIO_SECRET',$generalData->twilio_secret_key);
                Cache::forever('TWILIO_TOKEN',$generalData->twilio_token);
            }
        }
    }
}
