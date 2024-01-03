<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Redirect;
use Config;

// used for local cache busting and remote testing

class LogsViewerController extends Controller
{

     /**
      * load the logs viewer page
      *
      * @link GET:/logsviewer/{logname?}
      * @param Request $request
      * @param string $logname
      * @return string html
      */
    public function index(Request $request, $logname = null)
    {
        $logs = null;
        $lognames = [];

        if ($request->session()->has('loggedin')) {
            $logsFolder = dirname(dirname(dirname(dirname(__FILE__)))) . "/public/logs";

            $lognames = $this->getLogNames($logsFolder);

            if (!empty($logname)) {
                $logFile = $logsFolder . "/" . $logname  . ".log";

                if (file_exists($logFile)) {
                    $logs = file($logFile);
                    $logs = array_reverse($logs);
                    $logs = implode("<hr>", $logs);
                }
            }
 
            return view('logsviewer', ['logname' => $logname, 'logs' => $logs, 'lognames' => $lognames]);
        } else {
            return view('landing');
        }
    }


    /**
      * load the logs viewer page
      *
      * @link GET:/deletelog/{logname}
      * @param Request $request
      * @param string $logname
      * @return string html
      */
    public function deleteLog(Request $request, $logname)
    {
        $logs = null;
        $lognames = [];
  
        if ($request->session()->has('loggedin')) {
            $logsFolder = dirname(dirname(dirname(dirname(__FILE__)))) . "/public/logs";
  
            // Check if the directory exists
            if (file_exists($logsFolder) && is_dir($logsFolder)) {
                $logFile = $logsFolder  . '/' . $logname . '.log';

                if (file_exists($logFile)) {
                    unlink($logFile);
                }
            }

            $lognames = $this->getLogNames($logsFolder);
   
            return view('logsviewer', ['logname' => $logname, 'logs' => $logs, 'lognames' => $lognames]);
        } else {
            return view('landing');
        }
    }


    /**
      * Create a cron job request to restart the consumer
      *
      * @link GET:/restart
      * @param Request $request
      * @return string html
      */
    public function restartConsumer(Request $request)
    {
        $logs = null;
        $lognames = [];
    
        $logname = null;
        if ($request->session()->has('loggedin')) {

            $logsFolder = dirname(dirname(dirname(dirname(__FILE__)))) . "/public/logs";
  
            $jobsFolder = "/var/www/ruckify-builder/jobs";

            $signature = date("y_m_d_h_i_s") . "_messagerouter_restart";
            
            $builderJobFile = "{$jobsFolder}/build_{$signature}.job";

            $command = "( { ";
            $command .= dirname(dirname(dirname(dirname(__FILE__)))) . "/shell-scripts/consumer_restarter.sh;";
            $command .= "} )";

            // Check if the directory exists
            if (file_exists($jobsFolder) && is_dir($jobsFolder)) {               
                file_put_contents($builderJobFile, $command);
            }
  
            $lognames = $this->getLogNames($logsFolder);

            if (!empty($logname)) {
                $logFile = $logsFolder . "/" . $logname  . ".log";
  
                if (file_exists($logFile)) {
                    $logs = file($logFile);
                    $logs = array_reverse($logs);
                    $logs = implode("<hr>", $logs);
                }
            }


            $message = "Consumers restart cron created and consumers should restart in a few seconds.";
     
            return view('logsviewer', ['logname' => $logname, 'logs' => $logs, 'lognames' => $lognames, 'message' => $message]);
        } else {
            return view('landing');
        }
    }

      

    /**
     * Get all log files
     *
     * @return array
     */
    private function getLogNames($logsFolder)
    {
        $lognames = [];

        $scan_arr = scandir($logsFolder);
        $files_arr = array_diff($scan_arr, array('.','..'));

        foreach ($files_arr as $file) {
            $file_path = $logsFolder . '/'.$file;
            // Get the file extension
            $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);
            if ($file_ext=="log") {
                $lognames[] = str_replace(".log", "", $file);
            }
        }

        return $lognames;
    }
}
