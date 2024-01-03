@php 
$noAck = env('RABBITMQ_NO_ACK') ? 'true' : 'false';
@endphp 
<div class="row">
    <div class="col buildspecs mt-2">
        Branch: {{ getGitBranch() }}
        <br>
        Build: {{ lastBuildTime() }}
        <br>
        RabbitMQ: {{ env('RABBITMQ_QUEUE_SUFFIX') }}&#64;<a href="{{ env('RABBITMQ_GUI') }}" target="_blank">{{ env('RABBITMQ_HOST') }}</a> with noack: {{$noAck}}
        <br>
    </div>
</div>