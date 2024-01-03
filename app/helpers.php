<?php


/**
 * Get the current git branch
 * @return string
 */
function getGitBranch()
{
    $gitHeadFile = dirname(dirname(__FILE__)).'/.git/HEAD';
    if (file_exists($gitHeadFile)) {
        return trim(implode('/', array_slice(explode('/', file_get_contents($gitHeadFile)), 2)));
    }

    return null;
}


/**
 * get the date of the last run or install.sh
 *
 * @return void
 */
function lastBuildTime()
{
    $buildtimeFile = dirname(dirname(__FILE__)).'/buildtime.txt';
    if (file_exists($buildtimeFile)) {
        $timeString= file_get_contents($buildtimeFile);
        if (!empty($timeString)) {
            $timestamp = strtotime($timeString);
            if ($timestamp) {
                $date = new \DateTime();
                $date->setTimestamp($timestamp);
                $date->setTimezone(new DateTimeZone('America/New_York'));
                return  $date->format('Y-m-d H:i');
            }
        }
    }

    return null;
}

