<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Redirect;
use App\Extensions\AmqpConnectionChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

use Config;

// used for local cache busting and remote testing

class TestingToolsController extends Controller
{

    /**
     * load the Testing Tools  page

    * @param Request $re
    * @return strting html
    */
    public function index(Request $request)
    {
        if ($request->session()->has('loggedin')) {
            return view('testingtools');
        } else {
            return view('landing');
        }
    }

    /**
      * Send a test message
      *
      * @link
      * @param Request $request
      * @return void
      */
    public function sendMessage(Request $request)
    {

        $qnameSuffix = env('RABBITMQ_QUEUE_SUFFIX', null);

        if (!empty($qnameSuffix)) {
            $qnameSuffix = "_" . $qnameSuffix;
        }

        $input = $request->all();

        $relayMethod = $input['relay_method'];
        $email = $input['email'];

        if ($request->session()->has('loggedin')) {
            $payload = [
                'apikey' => Config::get('bunking.sync_apikey'),
                'action' => 'admin_notification',
                'sender_email' => env('SENDER_EMAIL', 'do-not-reply@bunking.com'),
                'sender_name' => 'Bunking Notifications',
                'recipient_email' => $email,
                'subject' => "Relay test subject via {$relayMethod}",
                'message' => "Relay test message via {$relayMethod}",
                'gitlog' => null,
                'file' => null,
                'function' => null,
                'url' => null,
                'ip' => null,
                'input' => null,
            ];

            if ($relayMethod === 'rabbitmq') {
                $syncResult = $this->rabbitMQRelay("admin_notification{$qnameSuffix}", $payload);
                return response()->json(['success' => $syncResult]);
            } elseif ($relayMethod === 'webhook') {
                $syncResult = $this->webhookRelay("relaymessage", $payload);
                return response()->json(json_decode($syncResult));
            }
        } else {
            return response()->json(['failure' => 'not logged in']);
        }
    }


    /**
    * Generate a fake website error
    *
    * @link
    * @param Request $request
    * @return string json
    */
    public function fakeWebsiteError(Request $request)
    {

        $qnameSuffix = env('RABBITMQ_QUEUE_SUFFIX', null);

        if (!empty($qnameSuffix)) {
            $qnameSuffix = "_" . $qnameSuffix;
        }

        $input = $request->all();
  
        $relayMethod = $input['relay_method'];
        $errorMessage = $input['errormessage'];
  
        if ($request->session()->has('loggedin')) {
            $payload = [
                'apikey' => Config::get('bunking.sync_apikey'),
                'action' => 'website_error_email',
                'sender_email' => 'do-not-reply@bunking.com',
                'sender_name' => 'Bunking Admin',
                'recipient_email' => 'dev@bunking.com',
                'subject' => "Bunking Fake Website Error: {$errorMessage}",
                'error' => $errorMessage,
                'gitlog' => null,
                'file' => null,
                'line' => null,
                'url' => null,
                'ip' => null,
                'geolocation' => null,
                'referer' => null,
                'input' => null,
            ];
  
            if ($relayMethod === 'rabbitmq') {
                $syncResult = $this->rabbitMQRelay("website_error_email{$qnameSuffix}", $payload);
                return response()->json(['success' => $syncResult]);
            } elseif ($relayMethod === 'webhook') {
                $syncResult = $this->webhookRelay("relaymessage", $payload);
                return response()->json(json_decode($syncResult));
            }
        } else {
            return response()->json(['failure' => 'not logged in']);
        }
    }

    /**
     * Connect to rabbitMQ and send message to remote processing.
     * This is what happens on calling services.
     *
     * @param string $qname name of the message queue
     * @param array $payload data to pass onto message consumer
     * @return string
     */
    private function rabbitMQRelay($qname, $payload)
    {
        if (is_array($payload)) {
            $payload = json_encode($payload);
        }

        // you can also just use  [ $connection, $channel ] = $this->setup();
        $rmq_host     = Config::get('bunking.rabbitmq_creds.host');
        $rmq_port     = Config::get('bunking.rabbitmq_creds.port');
        $rmq_user     = Config::get('bunking.rabbitmq_creds.user');
        $rmq_password = Config::get('bunking.rabbitmq_creds.password');
        $rmq_vhost    =  Config::get('bunking.rabbitmq_creds.vhost');
        
        try {
            $connection = new AMQPStreamConnection($rmq_host, $rmq_port, $rmq_user, $rmq_password);
            $channel    = $connection->channel();
            $msg        = new AMQPMessage($payload);
            $channel->queue_declare($qname, false, false, false, false);
            $channel->basic_publish($msg, '', $qname);
            $channel->close();
            $connection->close();
        } catch (Throwable $e) {
            return $e->getMessage();
        }

        return "Relay to RabbitMQ for {$qname} complete";
    }


    /**
     * Connect to message router directly via webhook
     *
     * @param string $webhookpath url path for webhook
     * @param array $payload data to pass onto message consumer
     * @return string
     */
    private function webhookRelay($webhookpath, $payload)
    {
        $messagerouterUrl = env('MESSAGEROUTER_URL', null);

        if (empty($messagerouterUrl)) {
            $messagerouterUrl = env('APP_URL', 'https://bunkingmessagerouter.ruckify.localhost');
        }

        $url = $messagerouterUrl."/$webhookpath";
        
        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_USERAGENT, 'Bunking API/1.0 (Bunking API)');
        curl_setopt($handler, CURLOPT_POST, true);
        curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($handler);

        return $response;
    }
}
