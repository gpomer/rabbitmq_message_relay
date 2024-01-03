<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Segment;
use Config;
use App\Helpers\AppHelper;

class SegmentController extends Controller
{
    private $log = true;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function processSegmentRequest($packet)
    {
        $logsFolder = dirname(dirname(dirname(dirname(__FILE__)))) . "/public/logs";
        $logfile = $logsFolder . "/segment_request.log";
        AppHelper::initLogFile($logfile);

        if($this->log) \Log::info(['packet json',$packet]);
        file_put_contents($logfile, $packet . PHP_EOL, FILE_APPEND | LOCK_EX);
        $packet_arr = json_decode($packet,true);
        $packet_arr = $packet_arr[0];
        if($this->log) \Log::info(['packet arr',$packet_arr]);
        $segment = false;
        if(empty($packet_arr) || empty($packet_arr['segment_entity']))
            return;
        
            switch ($packet_arr['segment_entity']) {
                case 'identify':
                    \Log::error('Segment: '.$packet_arr['segment_entity']);
                    $packet_array['data_x'] = [
                        'userId' => $packet_arr['user_uuid'],
                        'traits' => $packet_arr['data']
                    ];
                    $segment = true;
                break;
                case 'track':
                    \Log::error('Segment: '.$packet_arr['segment_entity']);
                    if(empty($packet_arr['segment_event'])) {
                        \Log::error(['Segment Data Error: missing segment_event',$packet_arr]);
                        return;
                    }
                    $packet_array['data_x'] = [
                        'userId' => $packet_arr['user_uuid'],
                        'event' => $packet_arr['segment_event'],
                        'properties' => $packet_arr['data']
                    ];
                    $segment = true;
                break;
                case 'screen':
                case 'page':
                    \Log::error('Segment: '.$packet_arr['segment_entity']);
                    if(empty($packet_arr['segment_name'])) {
                        \Log::error(['Segment Data Error: missing segment_name',$packet_arr]);
                        return;
                    }
                    $packet_array['data_x'] = [
                        'userId' => $packet_arr['user_uuid'],
                        'name' => $packet_arr['segment_name'],
                        'properties' => $packet_arr['data']
                    ];
                    $segment = true;
                break;
            }    

        if($segment) {
            $key = Config::get('segment.write_key');
            //SET SEGMENT KEY IN .env: SEGMENT_KEY=XXX
            //Defaulting to my LIMITED USE test account
            if(empty($key))
                $key = 'pHmg1E4wavNswYNeK9tPbf8DNBSue4nz';
            $data = $packet_array['data_x'];
            $entity = $packet_arr['segment_entity'];
            if($this->log) \Log::info(["SEGMENT CONNECT","{$entity}()",$data]);
            Segment::init($key);
            Segment::{$entity}($data);    
            file_put_contents($logfile, "SEGMENT CONNECTED: {$entity}()".json_encode($data) . PHP_EOL, FILE_APPEND | LOCK_EX);
        } else {
            if($this->log) \Log::info("SEGMENT CANNOT CONNECT");
            file_put_contents($logfile, "SEGMENT CANNOT CONNECT" . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        //we could send everything to a data warehouse here - both segment and none segment data put on this queue
    }
}
