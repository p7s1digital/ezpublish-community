<?php

class mxdJSON
{

    /**
     * decode the given jsonstring and return the array
     * this method throws an exception if an error occurs
     *
     * @param $jsonString
     * @return mixed
     * @throws Exceptiion
     * @throws Exception
     */
    public static function decode($jsonString)
    {
        if (false === is_string($jsonString)) {
            throw new Exceptiion('Expected a JSON-string for decoding');
        }

        $arr = json_decode($jsonString, true);
        $errNo = json_last_error();

        switch ($errNo) {
            case JSON_ERROR_NONE:
                return $arr;

            case JSON_ERROR_DEPTH:
                $errorMsg = ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $errorMsg = ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $errorMsg = ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $errorMsg = ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $errorMsg = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $errorMsg = ' - Unknown error';
        }

        $errorMsg .= ':' . var_export($jsonString, true);

        throw new Exception($errorMsg);
    }

}
