<?php


namespace App\Library\Services;

use App\User;
use Illuminate\Support\Facades\Log;
use Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class HubspotService
 * @package App\Library\Services
 */
class HubspotService
{

    private $endpoints;
    private $endpoint;
    private $objectTypeIds;
    private $objectTypeId;

    public function send($objectTypeId, $properties, $action, $primaryKey) {
	\Log::info([$primaryKey, $properties]);
        $this->endpoint = 'crm/v3/objects';
        $this->objectTypeId = $objectTypeId;
        $this->objectId = $properties[$primaryKey];
        if($action == 'update') {
            unset($properties[$primaryKey]);
	    }
        $this->_connect($properties, $action, $primaryKey);
    }

    private function _connect($properties, $action, $primaryKey) {
        $endpoint = 'https://api.hubapi.com/' .$this->endpoint;
            if($action == 'create') {
                $verb = 'POST';
                $endpoint .= "/{$this->objectTypeId}";
                $endpoint .= '?hapikey=' . Config::get('hubspot.HUBSPOT_API_KEY');
                $post_json = ['properties' => $properties];
            } else {
                $verb = 'PATCH';
                $endpoint .= "/{$this->objectTypeId}/{$this->objectId}";
                $endpoint .= '?hapikey=' . Config::get('hubspot.HUBSPOT_API_KEY');
                $endpoint .= '&idProperty='.$primaryKey;
                $post_json = ['properties' => $properties];
            }

            \Log::info([$action,$endpoint,$post_json]);
        $client = new Client();
        $res = $client->request($verb, $endpoint, [
            'json' => $post_json
        ]);
        return $res->getBody();
    }
} 
