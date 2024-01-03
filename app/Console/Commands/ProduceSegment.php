<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Faker\Factory;

use App\Extensions\AmqpConnectionChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * generate an admin notification and push to rabbitmq.
 * 
 * this file is for demo purposes 
 * 
 * queue can be viewed at http://3.21.227.236:15672/  user/pass admin/yuikopl
 * 
 * run this file from the console with:
 * php artisan massageproducer:example
 */
class ProduceSegment extends Command
{

    use AmqpConnectionChannel;

    /**yuikopl
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'segmentproducer {type? : must specify type of |page|screen|identify|event|}';

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

        $qname = "segment_request";
        $type = !empty($this->argument('type')) && in_array($this->argument('type'),['page','screen','track','identify']) ? $this->argument('type') : false;
        if(empty($type)) {
            echo "\nYou must enter a segment type as an argument to this command (identify/track/page/screen)\nEach of which has specific data requirements.\nAn example will be shown for the chosen type.\n";exit;
            exit;
        }
        echo "\nSending $type\n";
        [ $connection, $channel ] = $this->setup();
        $channel->queue_declare($qname, false, false, false, false);
 
        if($type == 'page' || $type == 'screen') {
            $payload = json_encode([
                'segment_entity' => $type,
                'segment_name' => 'login-'.$type,
                'user_uuid'=>'111',
                'data' => [
                    'name'=>'value',
                    'other_name'=>'other_value'
                ]
            ]);
        } else if ($type == 'identify') {
            $payload = json_encode([
                'segment_entity' => $type,
                'user_uuid'=>'111',
                'data' => [
                    'name'=>'value',
                    'other_name'=>'other_value'
                ]
            ]);
        } else if ($type == 'track') {
            $payload = json_encode([
                'segment_entity' => $type,
                'segment_event' => 'example track event',
                'user_uuid'=>'111',
                'data' => [
                    'name'=>'value',
                    'other_name'=>'other_value'
                ]
            ]);
        }

        echo "\nThe structure required by segment as defined for this example\n\n";
        var_dump($payload);
        $payload = new AMQPMessage($payload);
        $channel->basic_publish($payload, '', $qname);
        
        echo "Sent identity packet to segment\n";
        
        $channel->close();
        $connection->close();

    }
}
