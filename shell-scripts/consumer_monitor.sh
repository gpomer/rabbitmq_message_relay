#!/bin/bash

# checks the status of all consumers and restarts all if any have failed

# ensure this service is always running:
# */3 * * * * /var/www/message-router/development-messagerouter.ruckify.com/shell-scripts/consumer_monitor.sh

# To test you can kill the process with this command:
# kill -9 `ps aux | grep cachebusterconsumer | awk '{print $2}'`


current_dir="$(
    cd "$(dirname "$0")" >/dev/null 2>&1
    pwd -P
)"

parent_dir="$(dirname "$current_dir")"

BASENAME=`basename "$parent_dir"`

STARTCONSUMER="setsid $parent_dir/shell-scripts/message_consumer.sh --${BASENAME} > $parent_dir/public/logs/message_consumer.log "
LOGFILE="$parent_dir/public/logs/message_consumer.log"

failedprocess=false

#processcheck=$(ps aux | grep -i "messageconsumer --environment=${BASENAME}")

#if [[ $processcheck == *"php artisan messageconsumer --environment=${BASENAME}"* ]]; then
#    echo ""
#    echo "messageconsumer is running at $(date)"
#    echo ""
#else
#    echo ""
#    echo "messageconsumer is not running"
#    echo ""
#    failedprocess=true
#fi

#processcheck=$(ps aux | grep -i "segmentconsumer --environment=${BASENAME}")

#if [[ $processcheck == *"php artisan segmentconsumer --environment=${BASENAME}"* ]]; then
#    echo ""
#    echo "segmentconsumer is running at $(date)"
#    echo ""
#else
#    echo ""
#    echo "segmentconsumer is not running"
#    echo ""
#    failedprocess=true
#fi

processcheck=$(ps aux | grep -i "hubspotconsumer --environment=${BASENAME}")

if [[ $processcheck == *"php artisan hubspotconsumer --environment=${BASENAME}"* ]]; then
    echo ""
    echo "hubspotconsumer is running at $(date)"
    echo ""
else
    echo ""
    echo "hubspotconsumer is not running"
    echo ""
    failedprocess=true
fi

if [ "$failedprocess" == true ]; then

    echo ""
    echo "restarting consumers at $(date)"
    echo ""
    $STARTCONSUMER >>$LOGFILE

fi
