#!/bin/bash

# restart the message consumer once a day

# restart every 24 hours to keep log file light
# 5 5 * * * /var/www/message-router/development-messagerouter.ruckify.com/shell-scripts/consumer_restarter.sh

# ensure current process is dead so we don't end up with duplicate processes
kill -9 $(ps aux | grep message_consumer | awk '{print $2}')

current_dir="$(
    cd "$(dirname "$0")" >/dev/null 2>&1
    pwd -P
)"

parent_dir="$(dirname "$current_dir")"

BASENAME=`basename "$parent_dir"`

STARTCONSUMER="setsid $parent_dir/shell-scripts/message_consumer.sh --${BASENAME} > $parent_dir/public/logs/message_consumer.log "
LOGFILE="$parent_dir/public/logs/message_consumer.log"

#processcheck=$(ps aux | grep -i "messageconsumer --environment=${BASENAME}")

#if [[ $processcheck == *"php artisan messageconsumer --environment=${BASENAME}"* ]]; then
#    echo ""
#    echo "messageconsumer is running at $(date)"
#    echo ""
#    kill -9 `ps aux | grep -i "messageconsumer --environment=${BASENAME}" | awk '{print $2}'`
#fi

#processcheck=$(ps aux | grep -i "segmentconsumer --environment=${BASENAME}")

#if [[ $processcheck == *"php artisan segmentconsumer --environment=${BASENAME}"* ]]; then
#    echo ""
#    echo "segmentconsumer is running at $(date)"
#    echo ""
#    kill -9 `ps aux | grep -i "segmentconsumer --environment=${BASENAME}" | awk '{print $2}'`
#fi

processcheck=$(ps aux | grep -i "hubspotconsumer --environment=${BASENAME}")

if [[ $processcheck == *"php artisan hubspotconsumer --environment=${BASENAME}"* ]]; then
    echo ""
    echo "hubspotconsumer is running at $(date)"
    echo ""
    kill -9 `ps aux | grep -i "hubspotconsumer --environment=${BASENAME}" | awk '{print $2}'`
fi

echo "starting consumers at $(date)"
$STARTCONSUMER >> $LOGFILE
echo "Write restart log to $LOGFILE"



