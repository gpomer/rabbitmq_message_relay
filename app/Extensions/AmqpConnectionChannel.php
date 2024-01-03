<?php

namespace App\Extensions;

use PhpAmqpLib\Connection\AMQPStreamConnection;

use Config;
trait AmqpConnectionChannel
{
    public function setup()
    {

        $rmq_host     = Config::get('bunking.rabbitmq_creds.host');
        $rmq_port     = Config::get('bunking.rabbitmq_creds.port');
        $rmq_user     = Config::get('bunking.rabbitmq_creds.user');
        $rmq_password = Config::get('bunking.rabbitmq_creds.password');
        $rmq_vhost =  Config::get('bunking.rabbitmq_creds.vhost');

        $connection = new AMQPStreamConnection($rmq_host, $rmq_port, $rmq_user, $rmq_password, $rmq_vhost);
        $channel    = $connection->channel();

        return [ $connection, $channel ];
    }
}
