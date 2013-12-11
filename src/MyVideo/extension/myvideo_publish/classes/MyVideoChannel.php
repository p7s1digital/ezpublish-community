<?php
/**
 * User: Darius Sobczak
 * Date: 27.11.13
 * Utility class to avoid redundant data for channels
 */

class MyVideoChannel
{
    const TYPE_ICON = "icon";
    const TYPE_URL  = "url";

    const DEFAULT_CHANNEL_URL  = '/Serien/';

    protected static $channelMap = array(
        "Sat 1" => array(
            "icon" => "Sat1",
            "url" => "Sat_1"
        ),
        "ProSieben" => array(
            "icon" => "Pro7",
            "url" => "ProSieben"
        ),
        "Kabel Eins" => array(
            "icon" => "Kabel 1",
            "url" => "kabel_eins"
        ),
        "Anime TV" => array(
            "icon" => "Anime_TV",
            "url" => "AnimeTV"
        ),
        "BBC" => array(
            "icon" => "BBC",
            "url" => "BBC"
        ),
        "Sixx" => array(
            "icon" => "sixx",
            "url" => "sixx"
        ),
        "ProSieben MAXX" => array(
            "icon" => "ProSiebenMaxx",
            "url" => "ProSieben_MAXX"
        ),
        "YEP!" => array(
            "icon" => "YEP",
            "url" => "YEP"
        ),
        "Sony Retro" => array(
            "icon" => "SonyRetro",
            "url" => "Sony_Retro"
        ),
        "Your Family Entertainment" => array(
            "icon" => "YourFamilyEntertainment",
            "url" => "Your_Family_Entertainment"
        ),
        "Welt der Wunder" => array(
            "icon" => "WeltDerWunder",
            "url" => "Welt_der_Wunder"
        ),
        "Sat 1 Gold" => array(
            "icon" => "Sat1 Gold",
            "url" => "Sat_1_Gold"
        ),
        "Weitere Sender" => array(
            "icon" => "Weitere Sender",
            "url" => "Weitere_Sender"
        )
    );

    /**
     * @param $key
     * @return bool
     */
    public static function channelExists($key)
    {
        return array_key_exists($key, self::$channelMap) == true;
    }

    /**
     * @param $name
     * @return array|null
     */
    public static function getChannelData($key)
    {
        if(self::channelExists($key)) {
            $map = self::$channelMap[$key];

            return array(
                "name" => $key,
                "url" => self::DEFAULT_CHANNEL_URL . $map["url"],
                "icon" => $map["icon"],
                "iconChar" => MyVideoIcon::getCharacter($map["icon"])
            );
        }

        return null;
    }

    public static function replacePlaceholder($string, array $placeholders, $type = self::TYPE_URL)
    {
        foreach($placeholders as $placeholder => $key) {
            $string = self::replaceValue($string, $placeholder, $key, $type);
        }
        return $string;
    }

    /**
     * @param $string
     * @param $key
     * @param $value
     * @param string $type
     * @return string
     */
    protected static function replaceValue($string, $placeholder, $key, $type = self::TYPE_URL)
    {
        if(self::channelExists($key)) {
            $map = self::$channelMap[$key];

            if(array_key_exists($type, $map) == true) {
                return str_replace("{ $placeholder }", $map[$type], $string);
            }
        }

        return $string;
    }
}
