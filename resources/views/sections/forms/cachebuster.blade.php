<h5>Bust Cache</h5>
@php

$websiteDomain = "ruckify.com"; 
$sampleItemUuid = "fdec4e00-e869-11ea-90b1-a1c26e5b0ff4";
$sampleStoreUuid = "f2720760-d0dd-11ea-be6d-e13acb6085fb";

if (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false) {
    $websiteDomain = "ruckify.localhost";
    $sampleStoreUuid .= '-TEST';
} else if(strpos($_SERVER['SERVER_NAME'], 'development') !== false
    ||
    strpos($_SERVER['SERVER_NAME'], 'feature') !== false
) {
    $websiteDomain = "development.ruckify.com"; 
    $sampleStoreUuid .= '-TEST';
} else if (strpos($_SERVER['SERVER_NAME'], 'staging') !== false){
    $websiteDomain = "staging.ruckify.com"; 
    $sampleStoreUuid .= '-TEST';
}

@endphp

<ul>
<li class="mt-2">
Sample item uuid: <span class="samplecode">{{$sampleItemUuid}}</span> <a href="https://{{$websiteDomain}}/rental/{{$sampleItemUuid}}" target="_blank"><span class="icon-link"></span> view item</a>
</li>
<li class="mt-2">
Sample user uuid: <span class="samplecode">{{$sampleStoreUuid}}</span> <a href="https://{{$websiteDomain}}/ruckifystore/{{$sampleStoreUuid}}" target="_blank"><span class="icon-link"></span> view store</a>
</li>
</ul>


<form id="cachebuster-form">

    <div class="form-group">
        <label for="action">Cache Bust Type</label>
        <select class="form-control" name="action" id="cachebuster-action">
            <option value="">Select Action</option>
            <option value="syncitem">sync item</option>
            <option value="activateitem">activate item</option>
            <option value="updateitem">update item</option>
            <option value="deactivateitem">deactivate item</option>
            <option value="syncuser">sync user</option>
            <option value="activateuser">activate user</option>
            <option value="updateuser">update user</option>
            <option value="deactivateuser">deactivate user</option>
        </select>
    </div>
    <div class="form-group">
        <label for="uuid">UUID</label>
        <input type="text" class="form-control" name="uuid">
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="relay_method" value="rabbitmq"
            id="cachebuster_relay_method1">
        <label class="form-check-label" for="cachebuster_relay_method1">RabbitMQ</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="relay_method" value="webhook" id="cachebuster_relay_method2">
        <label class="form-check-label" for="cachebuster_relay_method2">Webhook</label>
    </div>
    <button id="processCacheBusterButton" type="button" class="btn btn-primary">Process</button>
</form>
<div class="alert alert-success d-none mt-3" id="cachebuster-response-display" role="alert"></div>
