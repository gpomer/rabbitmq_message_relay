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
class ProduceMessage extends Command
{
    use AmqpConnectionChannel;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'massageproducer:example';

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
        $qnameSuffix = env('RABBITMQ_QUEUE_SUFFIX', null);

        if (!empty($qnameSuffix)) {
            $qnameSuffix = "_" . $qnameSuffix;
        } else {
            $qnameSuffix = '';
        }

        $qname = "admin_notification{$qnameSuffix}";

        [ $connection, $channel ] = $this->setup();
        $channel->queue_declare($qname, false, false, false, false);

        $fakeName = Factory::create()->name;
        $fakeEmail = Factory::create()->email;
        $fakeSubject = Factory::create()->sentence(6, true);
        $fakeContent = Factory::create()->text;
 
        $payload = json_encode([
            'sender_email' => $fakeEmail,
            'sender_name' => $fakeName,
            'recipient_email' => $fakeEmail,
            'subject' => $fakeSubject,
            'message' => $fakeContent,
            'file' => '',
            'function' => '',
            'url' => '',
            'ip' => '',
            'input' => '',
        ]);
            
        $msg = new AMQPMessage($payload);
        $channel->basic_publish($msg, '', $qname);
        
        echo "Sent message to {$fakeName}\n";
        
        $channel->close();
        $connection->close();
    }
}
