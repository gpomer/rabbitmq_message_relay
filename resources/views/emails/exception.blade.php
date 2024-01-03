@if(!empty($payload['error']))
Error: {!! $payload['error'] !!}
@endif
@if(!empty($payload['gitlog']))
<br>
{!! $payload['gitlog'] !!}
@endif
@if(!empty($payload['file']))
<br>
File: {!! $payload['file'] !!}
@endif
@if(!empty($payload['line']))
<br>
Line: {!! $payload['line'] !!}
@endif
@if(!empty($payload['url']))
<br>
Url: {!! $payload['url'] !!}
@endif
@if(!empty($payload['ip']))
<br>
IP: {!! $payload['ip'] !!}
@endif
@if(!empty($payload['geolocation']))
<br>
Location: {!! $payload['geolocation'] !!}
@endif
@if(!empty($payload['input']))
<br>
input: {!! $payload['input'] !!}
@endif
@if(!empty($payload['referer']))
<br>
referer: {!! $payload['referer'] !!}
@endif