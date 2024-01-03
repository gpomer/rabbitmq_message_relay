<h5>Relay Message</h5>
<form id="relaymessage-form">
    <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" class="form-control" name="email" placeholder="Enter your email">
        <small class="form-text text-muted">This is the email we will send the
            test message to.</small>
    </div>

    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="relay_method" value="rabbitmq" id="message_relay_method1">
        <label class="form-check-label" for="message_relay_method1">RabbitMQ</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="relay_method" value="webhook" id="message_relay_method2">
        <label class="form-check-label" for="message_relay_method2">Webhook</label>
    </div>

    <button id="relaymessageButton" type="button" class="btn btn-primary">Send</button>
</form>

<div class="alert alert-success d-none mt-3" id="relaymessage-response-display" role="alert"></div>