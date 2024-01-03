<?php

namespace App\Helpers;

use App\AccessRules;
use App\Categories;
use App\Items;
use App\Jobs\ProcessMedia;
use App\Marketplaces;
use App\User;
use App\UserCredit;
use App\UserCreditAvailable;
use App\UserDetails;
use Cache;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OneSignal;
use Webpatser\Uuid\Uuid;

class AppHelper
{
    public static function instance()
    {
        return new AppHelper();
    }

    public static function initLogFile($logfile, $ttl = 4)
    {
        // if the log file is over 4 hours old delete it
        if (file_exists($logfile) && (time()-filemtime($logfile) > $ttl * 3600)) {
            unlink($logfile);
        }
    }

    /**
     * Process basic rabbitMQ ACK
     * 
     * If you set no-ack option of a consumer to true 
     * you should not call ack function manually.
     * 
     * @param  object $msg - rabbitmq consumer object
     * @return string - confirmation
     */
    public static function sendAck($msg)
    {

        $noAck = env('RABBITMQ_NO_ACK', false);

        if ($noAck === false) {

            if (!empty($msg->delivery_info['delivery_tag'])) {
                try {
                    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                } catch (Exception $e) {
                    $ackError = $e->getMessage();
                }
            }

            return " Sending aknowledgement back to rabbitMQ ";

        }

        return null;
       
    }
        
}
