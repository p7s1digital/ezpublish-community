<?php

class MyVideoRenderMyVideoPlayerPage extends MyVideoRendererBaseDynamicKey
{
    const TYPE = "PlayerPage";

    const DEFAULT_PLAYER = "Sidebar";
    const DEFAULT_TABMODUL = "PlayerTabModule";
    const DEFAULT_COLOR = "transparent";

    /**
     * The fixed part of the redis key,
     * mapped by the campaign prefix depending on the locales
     * @var array
     */
    protected $partIdentifierPrefix = array('default' => '', 'ger-DE' => '');

    /**
     * key parts
     *
     * @var array
     */
    protected $dynamicRedisKeyPartsKeys = array('url');

    /**
     * Builds the redis key for an ez publish object
     *
     * @param array $keyList list of content keys to become part of the redis key
     * @param array $ezObjectFields the ez object as array
     * @param bool $lookupOldKey indicated if an existing key should be generated
     * @return string
     */
    protected function buildRedisKeyString(array $keyList, array $ezObjectFields, $lookupOldKey = false)
    {
        $url = $ezObjectFields["url"];

        if ($lookupOldKey === false) {
            $url = $this->getAttributeUrl();
        } else {
            $url = $this->getAttributeUrl($url, $ezObjectFields["tv_channel"]["selected_values"][0]);
        }

        //remove /
        return substr($url, 1);
    }

    /**
     * @return array
     */
    public function render()
    {
        if ($this->getKeyToDelete()) {
            $this->deleteOldKey();
        }

        $redisData = array();

        $redisData['type'] = self::TYPE;

        $this->copyFieldContent("title", $redisData);
        $this->copyFieldContent("share_title", $redisData);
        $redisData['url'] = $this->getAttributeUrl();

        $this->writeStyleData($redisData);
        $this->writeHeaderData($redisData);
        $this->writeChannelData($redisData);
        $this->writePlayerData($redisData);

        $modules  = $this->renderAssetRelationList('modules');
        $this->addTabModul($modules);
        $redisData['modules'] = $modules;

        $this->copyFieldContent("banderole", $redisData);

        $redisData['simAdConfig'] = $this->renderMyVideoSimAdConfig();
        $redisData['tracking'] = $this->renderMyVideoTrackingData();
        $redisData['metaData'] = $this->renderMyVideoMetaData();

        return $redisData;
    }

    /**
     * write background and other styles parameter for redist
     *
     * @param array $redisData
     */
    protected function writeStyleData(array &$redisData)
    {
        $backgroundColor = $this->getAttributeValue("background_color");
        $backgroundImage = $this->getAttributeValue("background_image");

        $redisData["style"] = array(
            "image" => empty($backgroundImage) ? null : $backgroundImage["thumbnail"],
            "color" => empty($backgroundColor) ? self::DEFAULT_COLOR : $backgroundColor
        );
    }

    /**
     * write player modul (array) data
     *
     * @param array $redisData
     */
    protected function writePlayerData(array &$redisData)
    {
        $redisData["player"] = array(
            "type" => "Player",
            "layout" => self::DEFAULT_PLAYER,
            "autoStart" => $this->getAttributeValue("player_autostart"),
            "playListIndex" => $this->getAttributeValue("player_list_index"),
            "sidebarVideoList" => $this->getAttributeValue('player_sidebar_videolist')
        );
    }

    /**
     * write channel modul (array) data
     *
     * @param array $redisData
     */
    protected function writeChannelData(array &$redisData)
    {
        $tvChannel = $this->getAttributeSelectionValue('tv_channel');

        if (MyVideoChannel::channelExists($tvChannel) == true) {
            $redisData["channel"] = MyVideoChannel::getChannelData($tvChannel);
        }
    }

    /**
     * write header data
     *
     * @param array $redisData
     */
    protected function writeHeaderData(array &$redisData)
    {
        $imageTeaser = $this->getAttributeValue('header_image_teaser');

        if (empty($imageTeaser) == false) {
            $leftOffset = $this->getAttributeValue("header_image_teaser_left_offset");
            $topOffset = $this->getAttributeValue("header_image_teaser_top_offset");

            $redisData["header"] = array(
                "left" => $leftOffset,
                "top" => $topOffset,
                "image" => $imageTeaser
            );
        } //if(empty($imageTeaser) == false)
    }

    /**
     * add tab as an extra modul
     *
     * @param array $modules a array list of all available modules
     */
    protected function addTabModul(array &$modules)
    {
        $tabnames = $this->getAttributeValue("tab_modul_tabnames");
        $position = (int) $this->getAttributeValue("tab_modul_position");
        $icon = $this->getAttributeValue("tab_modul_icon");

        $tabmodul = array(
            "type" => self::DEFAULT_TABMODUL,
            "title" => $this->getAttributeValue("tab_modul_title"),
            "iconChar" => MyVideoicondropdowntype::mapDropDownKeyToChar($icon),
            "tabNames" => is_string($tabnames) ? explode(",", $tabnames) : "",
            "tabIndex" => (int) $this->getAttributeValue("tab_modul_tabindex") - 1,
            "items" => $this->renderAssetRelationList('tab_modul_items')
        );

        array_splice($modules, $position, 0, array($tabmodul));
    }

    /**
     * @param string $key Key name
     * @param string $type Type name DEFAULT value
     * @return mixed
     */
    protected function getAttributeSelectionValue($key, $type = "value")
    {
        $selection = $this->getAttributeValue($key);
        return $selection["selected_{$type}s"][0];
    }


    /**
     * return parsed url attribute value
     *
     * @return string
     */
    protected function getAttributeUrl($url = null, $channel = null)
    {
        if (is_string($url) !== true) {
            $url = $this->getAttributeValue("url");
        }

        $url = MyVideoChannel::replacePlaceholder(
            $url,
            array(
                'tv_channel' => is_string($channel) ? $channel : $this->getAttributeSelectionValue('tv_channel')
            )
        );

        return $url;
    }

}
