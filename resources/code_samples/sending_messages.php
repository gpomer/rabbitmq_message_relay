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