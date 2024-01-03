<?php

namespace App\Helpers;

use Exception;
use App\Helpers\AppHelper;
use App\ExceptionErrors;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Mail;

class MessagesHelper
{
    public static function instance()
    {
        return new MessagesHelper();
    }
    
    /**
     * processs message relay coming from webhook calls
     *
     * @param  object $msg
     * @return void
     */
    public static function processMessageRelay($msg)
    {
        $relayreport = null;

        $payload = json_decode($msg->body, true);

        $esreport = null;
        $action = $payload['action'];

        if (json_last_error() === JSON_ERROR_NONE) {
            switch ($action) {

            case "website_error_email":
                $relayreport = self::processWebsiteError($msg);
                break;
            }
        }

        return $relayreport;
    }

    /**
     * Email website errors
     *
     * @param  object $msg
     * @return string
     */
    public static function processWebsiteError($msg)
    {
        $response = null;

        $relayMethod = app()->runningInConsole() ? 'rabbitmq' : 'webhook';

        $logsFolder = dirname(dirname(dirname(__FILE__))) . "/public/logs";
        $logfile = $logsFolder . "/website_error_email_{$relayMethod}.log";
        $errorfile = $logsFolder . "/website_error_email_{$relayMethod}_errors.log";

        AppHelper::initLogFile($logfile);
           
        $payload = json_decode($msg->body, true);

        ExceptionErrors::logError('website', $payload);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            try {
                Mail::send(
                    'emails.exception',
                    ['payload' => $payload],
                    function ($message) use ($payload) {
                        $message->to($payload['recipient_email'])
                            ->from($payload['sender_email'])
                            ->replyTo($payload['sender_email'], $payload['sender_name'])
                            ->subject($payload['subject']);
                    }
                );

                $timestamp = date("Y-m-d h:i:s");
                $response = "Processed website error notification at {$timestamp}: ". $msg->body;

                $response .= AppHelper::sendAck($msg);
                file_put_contents($logfile, $response . PHP_EOL, FILE_APPEND | LOCK_EX);
            } catch (Exception $e) {
                AppHelper::sendAck($msg);
                $error = $e->getMessage();
                file_put_contents($errorfile, "Failed to send website error notification:" . $error  . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        }

        return $response;
    }
}
