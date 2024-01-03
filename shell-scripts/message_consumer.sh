#!/bin/bash

# queue can be viewed at http://3.21.227.236:15672/  user/pass admin/yuikopl

# make sure to set this file as executable with chmod +x *.sh

# add this to the cron
# restart the message consumer on reboot
# @reboot /var/www/message-router/development-messagerouter.ruckify.com/shell-scripts/consumer_restarter.sh
# restart every 24 hours to keep log file light
# 5 5 * * * /var/www/message-router/development-messagerouter.ruckify.com/shell-scripts/consumer_restarter.sh
# ensure this service is always running:
# */3 * * * * /var/www/message-router/development-messagerouter.ruckify.com/shell-scripts/consumer_monitor.sh

# for testing can be killed with: kill -9 `ps aux | grep message_consumer | awk '{print $2}'`

for var in "$@"; do
    if [ "$var" == "wait" ]; then
        # give rabbitmq time to start
        sleep 30
    fi
done

current_dir="$(
    cd "$(dirname "$0")" >/dev/null 2>&1
    pwd -P
)"

parent_dir="$(dirname "$current_dir")"

BASENAME=`basename "$parent_dir"bunking`

consumer_log_dir="$parent_dir/public/logs"

echo "RESTARTING RABBITMQ CONSUMERS: $(date)"

if [ ! -d "$consumer_log_dir" ]; then
    mkdir $consumer_log_dir
fi

# get rid of the old log files so they do not become enormous
if [ -f "$consumer_log_dir/website_error_email.log" ]; then
    sudo rm $consumer_log_dir/website_error_email.log
fi

if [ -f "$consumer_log_dir/segment_request.log" ]; then
    sudo rm $consumer_log_dir/segment_request.log
fi

if [ -f "$consumer_log_dir/hubspot_request.log" ]; then
    sudo rm $consumer_log_dir/hubspot_request.log
fi

cd $parent_dir
#php artisan messageconsumer --environment=$BASENAME &
#php artisan segmentconsumer --environment=$BASENAME &
php artisan hubspotconsumer --environment=$BASENAME &
echo "RUNNING CONSUMERS"
