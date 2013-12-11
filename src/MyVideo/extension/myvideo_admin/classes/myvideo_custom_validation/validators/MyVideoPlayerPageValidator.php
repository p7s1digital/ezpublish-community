<?php

/**
 * Class MyVideoPlayerPageValidator
 * Player Page Validator
 */

class MyVideoPlayerPageValidator extends MyVideoAbstractValidator
{
    const ATTR_TV_CHANNEL = "tv_channel";
    const ATTR_URL = "url";
    const ATTR_TAB_MODULE_TABINDEX = "tab_modul_tabindex";
    const ATTR_TAB_MODULE_POSITION = "tab_modul_position";
    const ATTR_PLAYER_LIST_INDEX = "player_list_index";
    const ATTR_PLAYER_SIDEBAR_VIDEOLIST = "player_sidebar_videolist";
    const ATTR_TAB_MODULE_ITEMS = "tab_modul_items";
    const ATTR_MODULES = "modules";

    const URL_TV_CHANNEL_REPLACER = "{ tv_channel }";

    const ERROR_TV_CHANNEL_URL = 'Es wurde kein Channel gewählt aber es befindet sich der Platzhalter in der URL';
    const ERROR_URL_WRONG = 'Die gewählte URL entspricht nicht der Vorgabe, es müssen mindestens zwei Stufen vorhanden sein. Beispiel /Serien/SerieX';
    const ERROR_TABINDEX_TO_HIGH = "Der Tabindex entspricht nicht der Anzahl der Elemente im Tab";
    const ERROR_TAB_POSITION_TO_HIGH = "Die Tabposition ist zu hoch. Es befinden sich zu wenige Module in der Liste";
    const ERROR_PLAYER_LIST_INDEX = "Es sind zu wenige Videolisten vorhanden, worauf der Player zugreifen kann. Bitte stellen Sie den Player Index niedriger";

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     * @param ezContentObjectAttribute $currentAttribute
     *
     * @return MyVideoCustomValidationResult
     */
    public function validateAttribute($allAttributes, $currentAttribute)
    {
        $attributeName = $currentAttribute->contentClassAttributeIdentifier();

        switch ($attributeName) {
            case self::ATTR_TV_CHANNEL:
                return $this->validateTvChannel($currentAttribute, $allAttributes);

            case self::ATTR_URL:
                return $this->validateUrl($currentAttribute, $allAttributes);

            case self::ATTR_TAB_MODULE_TABINDEX:
                return $this->validateTabModuleTabIndex($currentAttribute, $allAttributes);

            case self::ATTR_TAB_MODULE_POSITION:
                return $this->validateTabModulePosition($currentAttribute, $allAttributes);

            case self::ATTR_PLAYER_LIST_INDEX:
                return $this->validatePlayerListIndex($currentAttribute, $allAttributes);

        } //switch ($attributeName)

        return null;
    }

    /**
     * @param ezContentObjectAttribute $attribute
     * @param ezContentObjectAttribute[] $allAttributes as pointer
     *
     * @return MyVideoCustomValidationResult|null
     */
    public function validateTvChannel($attribute, &$allAttributes)
    {
        $url = $this->helper->getAttributeByNameFromArray($allAttributes, self::ATTR_URL);
        $url = $url->content();

        // channel value
        $value = $attribute->content();
        $value = (int) $value[0];

        // 0 is none value
        if($value == 0 && empty($url) == false) {
            if(strpos($url, self::URL_TV_CHANNEL_REPLACER) !== false) {
                return new MyVideoCustomValidationResult(
                    MyVideoCustomValidationResult::STATUS_ERROR,
                    array (
                        self::ERROR_TV_CHANNEL_URL
                    )
                );
            }
        }

        return null;
    }

    /**
     * @param ezContentObjectAttribute $attribute
     * @param ezContentObjectAttribute[] $allAttributes as pointer
     *
     * @return MyVideoCustomValidationResult|null
     */
    public function validateUrl($attribute, &$allAttributes)
    {
        $url = $attribute->content();

        // error if url not have subparts like /serie/xxx
        $parts = explode("/", $url);

        if(count($parts) < 3) {
            return new MyVideoCustomValidationResult(
                MyVideoCustomValidationResult::STATUS_ERROR,
                array (
                    self::ERROR_URL_WRONG
                )
            );
        }


        return null;
    }

    /**
     * @param ezContentObjectAttribute $attribute
     * @param ezContentObjectAttribute[] $allAttributes as pointer
     *
     * @return MyVideoCustomValidationResult|null
     */
    public function validateTabModuleTabIndex($attribute, &$allAttributes)
    {
        $index = (int) $attribute->content();

        if($index > 0) {
            $videolists = $this->getAttributeRelationList($allAttributes, self::ATTR_TAB_MODULE_ITEMS);

            if(count($videolists) < $index) {
                return new MyVideoCustomValidationResult(
                    MyVideoCustomValidationResult::STATUS_ERROR,
                    array (
                        self::ERROR_TABINDEX_TO_HIGH
                    )
                );
            }
        }

        return null;
    }

    /**
     * @param ezContentObjectAttribute $attribute
     * @param ezContentObjectAttribute[] $allAttributes as pointer
     *
     * @return MyVideoCustomValidationResult|null
     */
    public function validateTabModulePosition($attribute, &$allAttributes)
    {
        $index = (int) $attribute->content();

        if($index > 0) {
            $modules = $this->getAttributeRelationList($allAttributes, self::ATTR_MODULES);
            $modulCount = count($modules);

            if($modulCount < $index) {
                return new MyVideoCustomValidationResult(
                    MyVideoCustomValidationResult::STATUS_ERROR,
                    array (
                        self::ERROR_TAB_POSITION_TO_HIGH
                    )
                );
            }
        } //if($index > 0)

        return null;
    }

    /**
     * @param ezContentObjectAttribute $attribute
     * @param ezContentObjectAttribute[] $allAttributes as pointer
     *
     * @return MyVideoCustomValidationResult|null
     */
    public function validatePlayerListIndex($attribute, &$allAttributes)
    {
        $index = (int) $attribute->content();

        if($index > 0) {
            $videoListCount = 1; //first videolist is the sidebar (required)

            $tabItems = $this->getAttributeRelationList($allAttributes, self::ATTR_TAB_MODULE_ITEMS);
            $modules = $this->getAttributeRelationList($allAttributes, self::ATTR_MODULES);

            $videoListCount += count($tabItems);
            $videoListCount += count($modules);


            if($videoListCount < $index) {
                return new MyVideoCustomValidationResult(
                    MyVideoCustomValidationResult::STATUS_ERROR,
                    array (
                        self::ERROR_PLAYER_LIST_INDEX
                    )
                );
            }
        } //if($index > 0)

        return null;
    }

    protected function getAttributeRelationList(&$allAttributes, $key)
    {
        $data = $this->helper->getAttributeByNameFromArray($allAttributes, $key);
        if($data) {
            $data = $data->content();

            if(is_array($data)) {
                return $data["relation_list"];
            }
        }

        return null;
    }
}
