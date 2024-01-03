<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use App\Helpers\AppHelper;
use App\Library\Services\HubspotServices;
use App\Jobs\ProcessHubspot;
#use Request;

class HubspotController extends Controller
{
    private $log = true;

    public function processHubspotRequestWeb() {
        $packet = request()->get('packet');
        $this->processHubspotRequest($packet);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function processHubspotRequest($packet)
    {
        $action = 'create';
        $hubspot_config = config::get('hubspot');
        $objectTypeIds = $hubspot_config['objectTypeIds'];
        $primaryKeyValues = $hubspot_config['primaryKeys'];
        $packetArray[] = json_decode($packet, true);
        if($this->log) \Log::info(['packetArray',$packetArray]);

        foreach($packetArray as $object) {
            if($this->log) \Log::info(['object', $object]);
            if(empty($object['caller']) || empty($object['action'])) {
                continue;
            }
            $action = $object['action'];            
            $caller = $object['caller'];            
            foreach($primaryKeyValues as $primaryKeyValue) {
                if(!empty($object['properties'][$primaryKeyValue])) {
                    $primaryKey = $primaryKeyValue;
                    $objectTypeId = $objectTypeIds[$primaryKeyValue];
                }
            }
            if(!empty($objectTypeId) && !empty($object) && !empty($action) && !empty($primaryKey) && !empty($caller))
	            ProcessHubspot::dispatch($objectTypeId, $object, $action, $primaryKey, $caller);
                if($this->log) \Log::info(["(!empty($objectTypeId) && !empty($action) && !empty($primaryKey) && !empty($caller))", json_encode($object)]);
            }
    }
}

        /*
        A packet can look like this:

NOTES: 
- `action` MUST be supplied with value `create` the first time object is referenced so a new record can be created
- `action` property is optional, if it is NOT provided it will be assumed = `update`
    - SO if the object does not exist and action=`create` is not received ... nothing will happen...
- `wishpod_uuid` OR `bunkingpod_uuid` (etc.) IS THE unique primary key for everything EXCEPT A USER
- `bunking_user_id` IS THE unique user record primary key
- Every WishPod|BunkingPod object must reference a valid BunkingUser `bunking_user_id` (in Hubspot)
- For MULTI-SELECT send the value as "option1;option2;option3"
- It would be best to send `bunking_user_id` property when creating the POD objects otherwise the pod object will not be associated with the user in HubSpot until that is received.
[
    {
        "action":"create",
        "wishpod_uuid" : "123456789",
        "properties" : [
            {
                "property_name": "property_value",
                "property_name2": "property_value2",
                "property_name3": "property_value3"
            }
        ]
    }
]
OR
[
    {
        "action":"create",
        "bunking_user_uuid" : "123456789",
        "properties" : [
            {
                "property_name": "property_value",
                "property_name2": "property_value2",
                "property_name3": "property_value3"
            }
        ]
    }    
]

        */
