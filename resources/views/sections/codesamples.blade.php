@php
$codeSampleDir = dirname(dirname(dirname(dirname(__FILE__)))) . "/resources/code_samples";
@endphp

<strong>Sending Messages</strong>
<div class="bunking-code-container">
    {!! highlight_file($codeSampleDir . "/sending_messages.php", true) !!}
</div>
<br>

<strong>RabbitMQ Call</strong>
<div class="bunking-code-container">
    {!! highlight_file($codeSampleDir . "/rabbitmq.php", true) !!}
</div>

<br>
<strong>Webhook Call</strong>
<div class="bunking-code-container">
    {!! highlight_file($codeSampleDir . "/webhook.php", true) !!}
</div>
<br>




<hr>