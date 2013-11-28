<?php
/**
 * MyVideoUtils contains some simple static utility and helper methods
 */
class MyVideoUtils
{
    /**
     * Returns the given camelCase string to lower_case
     *
     * @param $str
     * @return mixed
     */
    public static function fromCamelCase($str)
    {
        $str[0] = strtolower($str[0]);
        return preg_replace('/([A-Z])/e', "'_' . strtolower('\\1')", $str);
    }

    /**
     * Returns the given lower_case string to camelCase
     *
     * @param $str
     * @param bool $capitaliseFirstChar
     * @return mixed
     */
    public static function toCamelCase($str, $capitaliseFirstChar = false)
    {
        $str = strtolower($str);
        if ($capitaliseFirstChar && strlen($str)) {
            $str[0] = strtoupper($str[0]);
        }

        return preg_replace('/_([a-z])/e', "strtoupper('\\1')", $str);
    }

    /**
     * Converts the keys in an array of arrays into camelcase
     * @param array $list
     * @param bool $capitaliseFirstChar
     * @return array
     */
    public static function arrayToCamelCase(array $list, $capitaliseFirstChar = false)
    {
        $tip = null;
        foreach ($list as $index => $entry) {
            foreach ($entry as $fieldName => $value) {
                unset($entry[$fieldName]);
                $tip[self::toCamelCase($fieldName, $capitaliseFirstChar)] = $value;
            }
            $list[$index] = $tip;
        }
        return $list;
    }

    /**
     * Retrieves the last archived version of a content object.
     *
     * @param eZContentObject $contentObject
     * @param string $language
     * @return array|null Delivers an array with the attributes or null.
     */
    public static function fetchLastObjectData(&$contentObject, $language)
    {
        $versions = self::fetchObjectVersions($contentObject);
        if (0 == count($versions)) {
            return null;
        }
        // we want the reverse order of versions
        $versions = array_reverse($versions);
        $version = null;
        // and take the latest with STATUS_ARCHIVED
        foreach ($versions as $versionObject) {
            if ($versionObject->Status == eZContentObjectVersion::STATUS_ARCHIVED) {
                $version = $versionObject->Version;
                break;
            }
        }

        return self::fetchObjectDataByVersion($contentObject, $version, $language);
    }

    /**
     * Delivers an array with all available version of an object. Ordered from oldest to newest.
     * The key is the version id, the values are instances of eZContentObjectVersion.
     *
     * @param eZContentObject $contentObject
     * @return array
     */
    public static function fetchObjectVersions(&$contentObject)
    {
        $versionList = array();
        // check if an older version is available to avoid querying the database for the complete record sets
        $lastObjectVersion = eZContentObjectVersion::fetchVersion(
            $contentObject->CurrentVersion - 1,
            $contentObject->ID
        );
        if (false === is_object($lastObjectVersion)) {
            return $versionList;
        }

        $versionObjectList = $contentObject->versions();
        foreach ($versionObjectList as $versionEntry) {
            $versionList[$versionEntry->Version] = $versionEntry;
        }
        return $versionList;
    }

    /**
     *
     * @param eZContentObject $contentObject
     * @param $version
     * @param $language
     * @return array
     */
    public static function fetchObjectDataByVersion(&$contentObject, $version, $language)
    {
        $dataMap = $contentObject->fetchDataMap($version, $language);
        $stack = array();
        foreach ($dataMap as $key => $contentObjectAttribute) {
            $fieldRenderer = MyVideoFieldRendererFactory::getInstance($contentObjectAttribute);
            $fieldRenderer->setLanguage($language);
            $result = $fieldRenderer->render();
            if (is_null($result)) {
                continue;
            }
            $stack[$key] = $result;
        }
        return $stack;
    }

    /**
     * Returns an url-safe version of the given string
     *
     * @param  string $str     The string to be made url safe
     * @return string
     */
    public static function makeUrlSafe($str)
    {
        $str = str_replace('ä', 'ae', $str);
        $str = str_replace('Ä', 'Ae', $str);
        $str = str_replace('ö', 'oe', $str);
        $str = str_replace('Ö', 'Oe', $str);
        $str = str_replace('ü', 'ue', $str);
        $str = str_replace('Ü', 'Ue', $str);
        $str = str_replace('ß', 'ss', $str);

        $str = preg_replace("/[^A-Za-z0-9]+/", "-", $str);
        $str = trim($str, "-");
        return $str;
    }
}
