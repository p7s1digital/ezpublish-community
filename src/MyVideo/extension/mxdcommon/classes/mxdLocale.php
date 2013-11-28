<?php

class mxdLocale extends mxdActiveRecord
{
    const TABLE_NAME = 'mxd_locales';

    /**
     * stores a list of all available locales
     * @var array:mxdLocale
     */
    private static $_locales;

    /**
     * (non-PHPdoc)
     * @see mxdActiveRecord::getTableName()
     */
    public function getTableName()
    {
        return self::TABLE_NAME;
    }

    /**
     * get a list of all available locales
     *
     * @return multitype:mxdLocale
     */
    public static function getAllLocales()
    {
        if (true === isset(self::$_locales)) {
            return self::$_locales;
        }

        $sql = 'SELECT * FROM ' . self::TABLE_NAME;
        $values = eZDB::instance()->arrayQuery($sql);

        self::$_locales = array();
        foreach ($values as $row) {
            $locale = new mxdLocale();
            $locale->initFromArray($row, false);
            self::$_locales[$locale->getId()] = $locale;

        }
        return self::$_locales;
    }

    /**
     * get a locale object for the given id
     *
     * @param $localeId
     * @return mixed
     * @throws Exception
     */
    public static function getLocaleById($localeId)
    {
        $locales = self::getAllLocales();
        if (false === isset($locales[$localeId])) {
            throw new Exception(sprintf('There is no locale for the given id "%s"', var_export($localeId, true)));
        }

        return $locales[$localeId];
    }
}
