<?php
/**
 * Class MyVideomenuurltype
 * The is a complex datatype for store URLs relative or static
 */
class MyVideomenuurltype extends eZDataType
{

    const PATTERN = '/^[a-z]+:\/\/[^\/]+/i';
    const DATA_TYPE_STRING = "myvideomenuurl";

    /**
     * constructor
     */
    public function __construct()
    {
        $this->eZDataType(self::DATA_TYPE_STRING, "MyVideo Menu URL", 'Datatype name',
            array('serialize_supported' => true,
                'object_serialize_map' => array('data_text' => 'text')));
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
        if ($http->hasPostVariable($base . "_data_boolean_" . $contentObjectAttribute->attribute("id"))) {
            $data = $http->postVariable($base . "_data_boolean_" . $contentObjectAttribute->attribute("id"));
            if (isset($data) && $data !== '0' && $data !== 'false') {
                $data = 1;
            } else {
                $data = 0;
            }
        } else {
            $data = 0;
        }
        $contentObjectAttribute->setAttribute("data_int", $data);

        if ($http->hasPostVariable($base . '_url_' . $contentObjectAttribute->attribute('id'))) {
            $url = $http->postVariable($base . '_url_' . $contentObjectAttribute->attribute('id'));
            $contentObjectAttribute->setAttribute('data_text', $url);
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
        return $contentObjectAttribute->attribute('data_text');
    }

    public function hasObjectAttributeContent($contentObjectAttribute)
    {
        return trim($contentObjectAttribute->attribute('data_text')) != '';
    }

    /**
     * Returns the meta data used for storing search indices.
     *
     * @param $contentObjectAttribute
     * @return string
     */
    public function metaData($contentObjectAttribute)
    {
        return $contentObjectAttribute->attribute('data_text');
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
        return $contentObjectAttribute->attribute('data_int') == 1 ? ' (extern)' : '';
    }

    /**
     * return true if the datatype can be indexed
     *
     * @return bool
     */
    public function isIndexable()
    {
        return true;
    }

    /**
     * return string representation of an contentobjectattribute data for simplified export
     *
     * @param $contentObjectAttribute
     * @return string
     */
    public function toString($contentObjectAttribute)
    {
        return $contentObjectAttribute->attribute('data_text');
    }

    /**
     * stores objects from string
     *
     * @param $contentObjectAttribute
     * @param $string
     * @return bool|void
     */
    public function fromString($contentObjectAttribute, $string)
    {
        if (preg_match(self::PATTERN, $string)) {
            $contentObjectAttribute->setAttribute('data_int', 1);
        }
        $contentObjectAttribute->setAttribute('data_text', $string);
        return true;
    }
}

eZDataType::register(myvideomenuurltype::DATA_TYPE_STRING, "myvideomenuurltype");
