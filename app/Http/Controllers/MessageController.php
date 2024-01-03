<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\MessagesHelper;
use Config;
use Faker\Factory;
use PhpAmqpLib\Message\AMQPMessage;

class MessageController extends Controller
{
    public $apiKey = null;

    public function __construct()
    {
        $this->apiKey = Config::get('bunking.sync_apikey');
    }

    /**
     * Call message relayer directly via webhook
     *
     * @link POST /cachebuster
     * @param Request $request
     * @return json
     */
    public function relayMessage(Request $request)
    {
        $input = $request->all();
        
        $apikey = !empty($input['apikey']) ? $input['apikey'] : null;
        $action = !empty($input['action']) ? $input['action'] : null;
        $recipient_email = !empty($input['recipient_email']) ? $input['recipient_email'] : null;

        if ($apikey !== $this->apiKey) {
            return response()->json(['failure' => 'api key invalid']);
        }

        $debug = !empty($_GET['debug']) ? true : false;
        
        if (!$debug) {
            $this->instantResponse("relaying message to {$recipient_email}");
        }

        $msg = new \stdClass();

        $msg->body = json_encode($input);

        // test to confirm instant response works correctly
        // sleep(2);
        // echo "ok";

        $response = MessagesHelper::processMessageRelay($msg);

        return response()->json(['success' => $response]);
    }
}
