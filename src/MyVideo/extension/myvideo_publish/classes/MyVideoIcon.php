<?php
/**
 * Created by JetBrains PhpStorm.
 * User: patrik jacoby
 * Date: 31.05.13
 * Utility class to avoid redundant data.
 */


class MyVideoIcon
{
    static private $iconMap = array(
        1 => 'Herz',
        2 => 'Filmklappe',
        3 => 'Fünf-Spitzen-Stern auf Abzeichen',
        4 => 'Sendemast mit Funkwellen',
        5 => 'Zwei Figuren hintereinander (Community)',
        6 => 'Fünf-Spitzen-Stern',
        7 => 'Uhr mit Zeigern auf 5 vor 12',
        8 => 'Ein-/Ausschalt-Button wie auf Fernbedienung',
        9 => 'Zahnrad',
        10 => 'Lupe',
        11 => 'Musiknote',
        12 => 'Fernseher',
        13 => 'Weihnachten',
        14 => 'Blume',
        15 => 'Schneefloke',
        16 => 'Stempel',
        17 => 'Rechteck mit Aufschrift LIVE',
        18 => 'Rechteck mit Aufschrift NEU',
        19 => 'Rechteck mit Aufschrift TOP',
        20 => 'Rechteck mit Aufschrift PREMIUM',
        21 => 'User',
        22 => 'Sprechblase',
        23 => 'Schloß',
        24 => 'Lorbeerkranz',
        25 => 'Theater',
        26 => 'Kino',
        27 => 'Filmstreifen',
        28 => 'Stern mit Ring',
        29 => 'Rechteck mit Aufschrift Trailer',
        30 => 'Rechteck mit Aufschrift Premiere',
        31 => 'Rechteck mit Aufschrift Ganze Folge',
        32 => 'Sport',
        33 => 'Pro7',
        34 => 'Sat1',
        35 => 'Kabel 1',
        36 => 'sixx bubble',
        37 => 'Rechteck mit Aufschrift Musik',
        38 => 'Rechteck mit Aufschrift Serie',
        39 => 'Rechteck mit Aufschrift Film',
        40 => 'ProSiebenMaxx',
        41 => 'YEP',
        42 => 'Sat1 Gold',
        43 => 'Weitere Sender',
        44 => 'sixx as letter'

    );
    /**
     * Map of ID => Character for the icons. See the styleguide as reference.
     * @var array
     */
    static private $characterMap = array(
        1 => 'A',
        2 => 'B',
        3 => 'C',
        4 => 'E',
        5 => 'F',
        6 => 'G',
        7 => 'J',
        8 => 'K',
        9 => 'L',
        10 => 'N',
        11 => 'O',
        12 => 'P',
        13 => 'Q',
        14 => 'R',
        15 => 'S',
        16 => 'T',
        17 => 'U',
        18 => 'V',
        19 => 'W',
        20 => 'X',
        21 => 'k',
        22 => 'l',
        23 => 'm',
        24 => 'n',
        25 => 'o',
        26 => 'p',
        27 => 'r',
        28 => 's',
        29 => 't',
        30 => 'u',
        31 => 'v',
        32 => 'w',
        33 => 'x',
        34 => 'y',
        35 => 'z',
        36 => '9',
        37 => '2',
        38 => '3',
        39 => '4',
        40 => 'underscore',
        41 => 'bracketleft',
        42 => 'slash',
        43 => 'unknown',
        44 => 'backslash'
    );

    /**
     * Delivers the full list of icon names with their label and identifier
     * @return array
     */
    public static function getList()
    {
        return self::$iconMap;
    }

    /**
     * @param $name string name of the icon
     * @return string The index of the icon in the internal array
     */
    public static function getIndex($name)
    {

        $identifier = null;
        if (in_array($name, self::$iconMap)) {
            $keys = array_keys(self::$iconMap, $name);
            $identifier = array_pop($keys);
        }
        return $identifier;
    }

    /**
     * @param $name string name of the icon
     * @return string The character of the icon (see styleguide for reference)
     */
    public static function getCharacter($name)
    {

        $character = null;
        $index = self::getIndex($name);
        if ($index) {
            $character = self::$characterMap[$index];
        }
        return $character;
    }

    /**
     * Tries to fetch the icon id from the given content item data.
     * Delivers the assigned character for the icon ID.
     *
     * @param $iconKey Key name of the icon field
     * @param $data    Data array of the content item.
     * @return string  Icon character
     */
    public static function getIconString($iconKey, &$data)
    {

        if (!empty($data['fields'][$iconKey]['content'])) {
            $tmp = $data['fields'][$iconKey]['content'];
            if (isset($tmp['selected_values'][0])) {
                return self::getCharacter($tmp['selected_values'][0]);
            }
        }
        return '';

    }

    /**
     * Generates the icon string from a content object.
     *
     * @param $data array contentItem as an array
     * @param string $iconKey       key for the first icon
     * @param string $secondIconKey key for the second icon
     * @return string
     */
    public static function generateIconString(&$data, $iconKey = 'icon', $secondIconKey = 'icon_2')
    {

        $icons = array();
        // the icon property is a string with a character for every icon, seperated by spaces
        $icons [] = MyVideoIcon::getIconString($iconKey, $data);
        $icons [] = MyVideoIcon::getIconString($secondIconKey, $data);
        $iconString = implode("", $icons);
        // the frontend is expecting a null for no icons
        if (strlen($iconString) == 0) {
            return null;
        }
        return $iconString;
    }
}
