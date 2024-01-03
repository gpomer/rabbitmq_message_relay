<?php

namespace App\Console\Commands;


use App\Helpers\MessagesHelper;

use Illuminate\Console\Command;

use App\Extensions\AmqpConnectionChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Http\Controllers\SegmentController;

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
class ConsumeSegment extends Command
{

    use AmqpConnectionChannel;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'segmentconsumer {--environment=}';

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
        $noAck = env('RABBITMQ_NO_ACK', false);
        
        if (!empty($qnameSuffix)) {
            $qnameSuffix = "_" . $qnameSuffix;
        } else {
            $qnameSuffix = '';
        }

        $segment_qname = "segment_request{$qnameSuffix}";

        $consumer_log_dir="/home/wwwbuilder/Desktop/consumer_logs";
        $segment_request_logfile = $consumer_log_dir . "/segment_request.log";

        [ $connection, $channel ] = $this->setup();

        $channel->queue_declare($segment_qname, false, false, false, false);

        echo " [*] Waiting for segment. To exit press CTRL+C\n";

        $segment_request_callback = function ($packet) {
            (new SegmentController)->processSegmentRequest($packet->body);
        };

        $channel->basic_consume($segment_qname, '', false, $noAck, false, false, $segment_request_callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

    }
}
