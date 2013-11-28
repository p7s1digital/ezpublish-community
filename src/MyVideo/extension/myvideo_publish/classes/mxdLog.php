<?php
/**
 * Simple wrapper class to eZLog
 */

class mxdLog
{
    /**
     * name of the logfile
     */
    const LOGFILE_NAME = 'redis.log';
    /**
     * directory of the logfile
     */
    const LOGFILE_DIRECTORY = 'var/log';

    /**
     * Helper method to write the given message to the log file
     *
     * @param null $msg
     */
    public static function write($msg = null)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        eZLog::write($msg . ' at ' . @$trace[1]['file'] . ':' . @$trace[1]['line'], self::LOGFILE_NAME, self::LOGFILE_DIRECTORY);
    }

    public static function dump($obj)
    {
        $msg = print_r($obj,true);
        self::write($msg);
    }
}
