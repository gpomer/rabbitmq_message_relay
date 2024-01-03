<?php

namespace App\Console\Commands;

use App\Helpers\MessagesHelper;

use Illuminate\Console\Command;

use App\Extensions\AmqpConnectionChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Mail;

/**
 * process notifications pushed to rabbitmq.
 *
 * queue can be viewed at http://3.21.227.236:15672/  user/pass admin/yuikopl
 *
 * run this file from the console with:
 * php artisan messageconsumer
 *
 * This needs to run perpetually
 * See: /home/ruckdev/Sites/ruckify-message-router/shell-scripts/consumer_restarter.sh
 */
class ConsumeMessages extends Command
{
    use AmqpConnectionChannel;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messageconsumer {--environment=}';

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

        $website_error_email_qname = "website_error_email{$qnameSuffix}";
        $admin_notification_qname = "admin_notification{$qnameSuffix}";


        [ $connection, $channel ] = $this->setup();

        $channel->queue_declare($website_error_email_qname, false, false, false, false);
        $channel->queue_declare($admin_notification_qname, false, false, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $website_error_email_callback = function ($msg) {
            MessagesHelper::processWebsiteError($msg);
        };

        $admin_notification_callback = function ($msg) {
            MessagesHelper::processAdminNotification($msg);
        };

        $channel->basic_consume($website_error_email_qname, '', false, $noAck, false, false, $website_error_email_callback);
        $channel->basic_consume($admin_notification_qname, '', false, $noAck, false, false, $admin_notification_callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
