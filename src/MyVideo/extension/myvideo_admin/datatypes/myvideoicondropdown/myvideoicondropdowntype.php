<?php

/**
 * Class MyVideoicondropdowntype
 * Datatype DropDown for icon selection
 *
 */
class MyVideoicondropdowntype extends eZDataType
{
    const PATTERN = '/^[0-9]*$/';
    const DATA_TYPE_STRING = "myvideoicondropdown";

    public function __construct()
    {
        $this->eZDataType(
            self::DATA_TYPE_STRING,
            'MyVideo Icon DropDown',
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
            if ($data < 0) {
                return false;
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

        $keyValueMap = self::buildKeyMap();
        if (in_array($string, $keyValueMap)) {
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
        $keyValueMap = self::buildKeyMap();
        if (array_key_exists($key, $keyValueMap)) {
            return $keyValueMap[$key];
        }
        // if the key is not available we deliver our default
        return $keyValueMap[0];
    }

    /**
     * @param $value
     * @return bool|mixed
     */
    public static function getKey($value)
    {
        $keyValueMap = self::buildKeyMap();
        // if the value is not available we deliver our default
        if (false === in_array($value, $keyValueMap)) {
            return false;
        }

        return array_search($value, $keyValueMap);
    }

    public static function getHtmlOptions($selected)
    {
        $keyValueMap = self::buildKeyMap();
        $html = "";
        foreach ($keyValueMap as $key => $value) {
            if ($selected == $key) {
                $html .= '<option selected="selected" value="' . $key . '">' . $value . '</option>';
            } else {
                $html .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        return $html;
    }

    public static function getCurrentIconName($selected)
    {
        $iconName = self::getIconName($selected);
        return $iconName;
    }

    public static function getIconName($dropDownKey)
    {
        // default to first map entry if obsolete content given
        $dropDownKey = 0;
        $keyMap = self::buildKeyMap();
        /* @todo check renderer for icon-drop-down method mapDropDownKeyToChar() is called with passed array-parameter*/
        if (is_array($dropDownKey) &&
            isset($dropDownKey["selected_keys"]) &&
            !empty($dropDownKey["selected_keys"])
        ) {
            $dropDownKey = $dropDownKey["selected_keys"][0];
        }
        $iconName = $keyMap[$dropDownKey];
        return $iconName;
    }

    public static function buildKeyMap()
    {
        $iconList = MyVideoIcon::getList();
        $emptyIcon = array(0 => "Kein Icon ausgewählt");
        $keyValueMap = array_merge($emptyIcon, $iconList);
        return $keyValueMap;
    }

    public static function mapDropDownKeyToChar($dropDownKey)
    {
        $iconName = self::getIconName($dropDownKey);
        $char = MyVideoIcon::getCharacter($iconName);
        return $char;
    }
}

eZDataType::register(MyVideoicondropdowntype::DATA_TYPE_STRING, "myvideoicondropdowntype");
