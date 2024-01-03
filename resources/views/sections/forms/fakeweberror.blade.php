<h5>Generate Fake Website Error</h5>
<form id="fakeweberror-form">
    <div class="form-group">
        <label for="errormessage">Error Message</label>
        <textarea class="form-control" name="errormessage">Fatal Error: all your base are belong to us.</textarea>

        <small class="form-text text-muted">This is the fake error.</small>
    </div>

    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="relay_method" value="rabbitmq" id="message_relay_method1">
        <label class="form-check-label" for="message_relay_method1">RabbitMQ</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="relay_method" value="webhook" id="message_relay_method2">
        <label class="form-check-label" for="message_relay_method2">Webhook</label>
    </div>

    <button id="fakeweberrorButton" type="button" class="btn btn-primary">Send</button>
</form>

<div class="alert alert-success d-none mt-3" id="fakeweberror-response-display" role="alert"></div>