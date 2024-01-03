<p>You have a new item search request from {{ $payload['name'] }}.</p>

<p>Contact Information:</p>

<p>
    <ul>
        <li>{{ $payload['name'] }}</li>
        <li>{{ $payload['phone'] }}</li>
        <li>{{ $payload['email'] }}</li>
        <li>{{ $payload['city'] }}</li>
    </ul>
</p>

Dates:
<p>
    <ul>
        <li>Start: {{ $payload['startdate'] }}</li>
        <li>End: {{ $payload['enddate'] }}</li>
    </ul>
</p>

<p>
Message: <p>{!! $payload['sender_message'] !!}</p>
</p>
