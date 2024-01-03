<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Library\Services\HubspotService;
use Config;

class ProcessHubspot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $log = true;
    public $tries = 5;

    private $object;
    private $objectTypeId;
    private $action;
    private $primaryKey;
    private $caller;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($objectTypeId, $object, $action, $primaryKey, $caller)
    {
        $this->objectTypeId = $objectTypeId;
        $this->object = $object;
        $this->action = $action;
        $this->primaryKey = $primaryKey;
        $this->caller = $caller;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $properties = $this->mapToUniqueKeys($this->caller, $this->object['properties']);
        try{
            $response = (new HubspotService)->send($this->objectTypeId, $properties, $this->action, $this->primaryKey);
            if(!empty($response) && !is_null($response)) {
                if($this->log) \Log::info(['JOB RESULT::',$response->getBody()]);
            } else {
                if($this->log) \Log::info(['JOB RESULT::','success']);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->failed($e);
            $delayInSeconds = $this->attempts() * 30;
            $this->release($delayInSeconds);
        }
    }

    private function mapToUniqueKeys($callingController, $data) {
        $modelName = $newKey = null;
        $processedData = [];
        $keyMapArray  = config::get('hubspot.property_map');    
        if($this->log) \Log::info([$keyMapArray[$callingController], $data]);
    	foreach($data as $key => $val) {
            if((is_array($val)) || is_object($val)) {
                foreach($val as $subKey => $subVal) {
                    $newSubKey = $this->getDescendantObjectPropValue($keyMapArray[$callingController], $key, $subKey);
                    $processedData[$newSubKey] = $val;        
                }
            } else {
                $newKey = $this->getDescendantPropValue($keyMapArray[$callingController], $key);
                $processedData[$newKey] = $val;        
            }
        }
        if($this->log) \Log::info(['processedData',$processedData]);
        return $processedData;
      }

    private function getDescendantPropValue($keyMapArray, $origKey) {
    	return $keyMapArray[$origKey];
    }

    private function getDescendantObjectPropValue($keyMapArray, $origKey, $subKey) {
    	return $keyMapArray[$origKey][$subKey];
    }

    public function failed($e)
    {
        \Log::info(['JOB FAILED::'.$e->getMessage(),$this->objectTypeId,$this->object]);
    }
}
