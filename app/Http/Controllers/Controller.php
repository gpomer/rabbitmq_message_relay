<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use PhpAmqpLib\Connection\AMQPStreamConnection;

use Config;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function setup()
    {

        $rmq_host     = Config::get('bunking.rabbitmq_creds.host');
        $rmq_port     = Config::get('bunking.rabbitmq_creds.port');
        $rmq_user     = Config::get('bunking.rabbitmq_creds.user');
        $rmq_password = Config::get('bunking.rabbitmq_creds.password');
        $rmq_vhost =  Config::get('bunking.rabbitmq_creds.vhost');

        $connection = new AMQPStreamConnection($rmq_host, $rmq_port, $rmq_user, $rmq_password);
        $channel    = $connection->channel();

        return [ $connection, $channel ];
    }

    /**
     * Send an instant response so we don't hold up the caller
     *
     * @param  string $response
     * @return void
     */
    public function instantResponse($response)
    {
        ignore_user_abort(true);
        set_time_limit(0);
        ob_start();

        echo json_encode(array("success" => $response ));

        $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
        header("Access-Control-Allow-Origin: *");
        header($serverProtocol.' 200 OK');
        header('Content-Encoding: none');
        header('Content-Length: '.ob_get_length());
        header('Connection: close');

        ob_end_flush();
   
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            flush();
        }
    }
}
