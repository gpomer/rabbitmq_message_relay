<?php

namespace App\Console\Commands;


use App\Helpers\MessagesHelper;

use Illuminate\Console\Command;

use App\Extensions\AmqpConnectionChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Http\Controllers\HubspotController;

/**
 * process notifications pushed to rabbitmq.
 * 
 * queue can be viewed at http://3.21.227.236:15672/  user/pass admin/yuikopl
 * 
 * run this file from the console with:
 * php artisan messageconsumer
 * 
 * This needs to run prepetually
 * See: /home/ruckdev/Sites/ruckify-message-router/shell-scripts/consumer_restarter.sh
 */
class ConsumeHubspot extends Command
{

    use AmqpConnectionChannel;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubspotconsumer {--environment=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $environment  = $this->option('environment');
        
        $qnameSuffix = env('RABBITMQ_QUEUE_SUFFIX', null);
        $noAck = env('RABBITMQ_NO_ACK', true);
        
        if (!empty($qnameSuffix)) {
            $qnameSuffix = "_" . $qnameSuffix;
        } else {
            $qnameSuffix = '';
        }

        $hubspot_qname = "hubspot_request{$qnameSuffix}";

        $consumer_log_dir="/var/www/bunking-message-router/storage/logs";
        $segment_request_logfile = $consumer_log_dir . "/hubspot_request.log";

        [ $connection, $channel ] = $this->setup();

        $channel->queue_declare($hubspot_qname, false, true, false, false);

        echo " [*] Waiting for hubspot. To exit press CTRL+C\n";

        $hubspot_request_callback = function ($packet) {
            (new HubspotController)->processHubspotRequest($packet->body);
        };

        $channel->basic_consume($hubspot_qname, '', false, $noAck, false, false, $hubspot_request_callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

    }
}
