<?php
/**
 * Datatype to store the type of the social widgets on a page.
 *
 * User: patrik Jacoby(patrik.jacoby@magicinternet.de)
 * Date: 24.07.13
 */

class MyVideosocialwidgettype extends eZDataType
{
    const PATTERN = '/^(0|1|2|3|4|5)/i';
    const DATA_TYPE_STRING = "myvideosocialwidget";
    /** @var array The mapping of the database values to human readable strings */
    static private $keyValueMap = array(
        0 => 'Kein Widget',
        1 => 'Facebook-MyVideo',
        2 => 'Facebook-MyVideo-Musik',
        3 => 'Facebook-MyVideo-Serien',
        4 => 'Facebook-MyVideo-Filme',
        5 => 'Facebook-MyVideo-Anime',
    );

    /**
     *
     */
    public function __construct()
    {
        $this->eZDataType(
            self::DATA_TYPE_STRING,
            'MyVideo Social Widget',
            array(
                'serialize_supported' => true,
                'translation_allowed' => false,
                'object_serialize_map' => array('data_int' => 'int')
            )
        );
    }

    /**
     * Validates all variables given on content class level
     * return eZInputValidator::STATE_ACCEPTED or eZInputValidator::STATE_INVALID if
     * the values are accepted or not
     *
     * @param $http
     * @param $base
     * @param $classAttribute
     * @return int
     */
    public function validateClassAttributeHTTPInput($http, $base, $classAttribute)
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Fetches all variables inputed on content class level
     * return true if fetching of class attributes are successfull, false if not
     *
     * @param $http
     * @param $base
     * @param $classAttribute
     * @return bool|void
     */
    public function fetchClassAttributeHTTPInput($http, $base, $classAttribute)
    {
        return true;
    }

    /**
     * Validates input on content object level
     * return eZInputValidator::STATE_ACCEPTED or eZInputValidator::STATE_INVALID if
     * the values are accepted or not
     *
     * @param $http
     * @param $base
     * @param $contentObjectAttribute
     * @return int
     */
    public function validateObjectAttributeHTTPInput($http, $base, $contentObjectAttribute)
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Fetches all variables from the object
     * return true if fetching of class attributes are successfull, false if not
     *
     * @param $http
     * @param $base
     * @param $contentObjectAttribute
     * @return bool|void
     */
    public function fetchObjectAttributeHTTPInput($http, $base, $contentObjectAttribute)
    {
        if ($http->hasPostVariable($base . "_data_int_" . $contentObjectAttribute->attribute("id"))) {
            $data = (int)$http->postVariable($base . "_data_int_" . $contentObjectAttribute->attribute("id"));
            if (empty($data)) {
                return false;
            }
            if ($data < 0 && $data > 3) {
                $data = 0;
            }
            $contentObjectAttribute->setAttribute("data_int", $data);
        }

        return true;
    }

    /**
     * Returns the content.
     *
     * @param $contentObjectAttribute
     * @return string
     */
    public function objectAttributeContent($contentObjectAttribute)
    {
        return $contentObjectAttribute->attribute('data_int');
    }

    public function hasObjectAttributeContent($contentObjectAttribute)
    {
        return trim($contentObjectAttribute->attribute('data_int')) != null;
    }

    /**
     * Returns the value as it will be shown if this attribute is used in the object name pattern.
     *
     * @param $contentObjectAttribute
     * @param null $name
     * @return string
     */
    public function title($contentObjectAttribute, $name = null)
    {
        return '';
    }

    /**
     * return true if the datatype can be indexed
     *
     * @return bool
     */
    public function isIndexable()
    {
        return false;
    }

    /**
     * return string representation of an contentobjectattribute data for simplified export
     *
     * @param $contentObjectAttribute
     * @return string
     */
    public function toString($contentObjectAttribute)
    {

        return (string)$contentObjectAttribute->attribute('data_int');
    }

    /**'object_serialize_map' => array('data_int' => 'int')
     * stores objects from string
     *
     * @param $contentObjectAttribute
     * @param $string
     * @return bool|void
     */
    public function fromString($contentObjectAttribute, $string)
    {
        if (preg_match(self::PATTERN, $string)) {
            $contentObjectAttribute->setAttribute('data_int', (int)$string);
            return true;
        }
        if (in_array($string, self::$keyValueMap)) {
            $value = self::getKey($string);
            if (false !== $value) {
                $contentObjectAttribute->setAttribute('data_int', $value);
                return true;
            }
        }
        return false;
    }

    /**
     * Delivers a string for the key selected
     *
     * @param $key int
     * @return string
     */
    public static function getEntry($key)
    {
        if (array_key_exists($key, self::$keyValueMap)) {
            return self::$keyValueMap[$key];
        }
        // if the key is not available we deliver our default
        return self::$keyValueMap[0];
    }

    /**
     * @param $value
     * @return bool|mixed
     */
    public static function getKey($value)
    {
        // if the value is not available we deliver our default
        if (false === in_array($value, self::$keyValueMap)) {
            return false;
        }

        return array_search($value, self::$keyValueMap);
    }
}

eZDataType::register(MyVideosocialwidgettype::DATA_TYPE_STRING, "myvideosocialwidgettype");
