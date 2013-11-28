<?php
/**
 * Simple wrapper class to eZLog
 */

class myvideoTrackingLog
{
    /**
     * name of the logfile
     */
    const LOGFILE_NAME = 'myvideotracking.log';
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
        eZLog::write($msg, self::LOGFILE_NAME, self::LOGFILE_DIRECTORY);
    }
}
