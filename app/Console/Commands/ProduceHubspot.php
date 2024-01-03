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
class ProduceHubspot extends Command
{

    use AmqpConnectionChannel;

    /**yuikopl
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubspot:producer {--type=} {--action=} {--wishpod_uuid=123456789123456789} {--bunking_user_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'hubspot:producer {--type=wishpod OR user OR mixed} {--action=create OR update} {?--wishpod_uuid=...} {?--bunking_user_id=...}';

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

        $qname = "hubspot_request";
        
        $type = !empty($this->option('type')) && (in_array($this->option('type'),['wishpod','user','mixed'])) ? $this->option('type') : false;

        $action = !empty($this->option('action')) && (in_array($this->option('action'),['create','update'])) ? $this->option('action') : false;

        $wishpod_uuid = $this->option('wishpod_uuid');

        $bunking_user_id = $this->option('bunking_user_id');

        if(empty($type) || empty($action)) {
            echo "\n$type|$action\n\n";
            echo "\nYou must enter BOTH a hubspot TYPE and ACTION as an argument to this command\n";
            echo "\nwishpod_uuid and bunking_user_id are optional, default to 123456789123456789 & 1 respectively\n";
            echo "\nhubspot:producer {--type=wishpod OR user OR mixed} {--action=create OR update} {?--wishpod_uuid=...} {?--bunking_user_id=...}\n\n";
            exit;
        }
        echo "\nSending $action $type\n";
        [ $connection, $channel ] = $this->setup();
        $channel->queue_declare($qname, false, false, false, false);
 
        if ($this->option('type') == 'wishpod') {
            $payload = array(
                [
                    'wishpod_uuid' => $wishpod_uuid,
                    'properties' => [
                        'bunking_user_id' => $bunking_user_id,
                        'wishpod_title' => 'Test Creating'
                    ],
                ]
            );
            if($this->option('action') == 'create') {
                $payload[0]['action'] = 'create';
            } else {
                $payload[0]['properties']['wishpod_title'] = 'Test Updating';
                $payload[0]['properties']['wishpod_host_intro_video'] = 'Test Update ADDING';
            }
        }

        if ($this->option('type') == 'user') {
            $payload = array(
                [
                    'bunking_user_id' => $bunking_user_id,
                    'properties' => [
                        'firstname' => 'Test UserFirstName',
                        'lastname' => 'Test UserLastName'
                    ],
                ]
            );
            if($this->option('action') == 'create') {
                $payload[0]['action'] = 'create';
            } else {
                $payload[0]['properties']['firstname'] = 'Test Update firstname';
                $payload[0]['properties']['lastname'] = 'Test Update lastname';
            }
        }

        if ($this->option('type') == 'mixed') {
            $payload = array(
                [
                    'action' => 'create',
                    'bunking_user_id' => $bunking_user_id,
                    'properties' => [
                        'firstname' => 'Test UserFirstName',
                        'lastname' => 'Test UserLastName'
                    ],
                ],
                [
                    'action' => 'create',
                    'wishpod_uuid' => $wishpod_uuid,
                    'properties' => [
                        'bunking_user_id' => $bunking_user_id,
                        'wishpod_title' => 'Test Creating'
                    ],
                ]
            );

            if($this->option('action') == 'update') {
                $payload[0]['action'] = 'update';
                $payload[1]['action'] = 'update';
            }
        }
        echo "\nThe structure required by hubspot as defined for this example\n\n";
        echo json_encode($payload, JSON_PRETTY_PRINT);//exit;
        $payload = json_encode($payload);
        $payload = new AMQPMessage($payload);
        $channel->basic_publish($payload, '', $qname);
        
        echo "\n\nSent contact packet to hubspot\n";
        
        $channel->close();
        $connection->close();

    }
}
