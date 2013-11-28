<?php

class MyVideoRenderMyVideoFormatPage extends MyVideoRendererBaseDynamicKey
{

    protected $partIdentifierPrefix = array('default' => 'TV/',
        'ger-DE' => 'TV/');

    protected $dynamicRedisKeyPartsKeys = array('tv_channel', 'url_identifier');


    public static $tvChannelMap = array(
        0 => "Sat_1",
        1 => "ProSieben",
        2 => "kabel_eins",
        3 => "Anime_TV",
        4 => "BBC",
        5 => "sixx",
        6 => "ProSieben_MAXX",
        7 => "YEP",
        8 => "Sony_Retro",
        9 => "Your_Family_Entertainment",
        10 => "Welt_der_Wunder",
        11 => "Sat_1_Gold",
        12 => "Weitere_Sender"
    );

    public static $tvChannelIconMap = array(
        0 => "Sat1",
        1 => "Pro7",
        2 => "Kabel 1",
        3 => "AnimeTV",
        4 => "BBC",
        5 => "sixx",
        6 => "ProSiebenMaxx",
        7 => "YEP",
        8 => "SonyRetro",
        9 => "YourFamilyEntertainment",
        10 => "WeltDerWunder",
        11 => "Sat1 Gold",
        12 => "Weitere Sender"
    );

    /**
     * TV channel is an array and has to be mapped to an entry to become part of the redis key
     * @var array
     */
    protected $keyPartOverride = array('tv_channel');

    /**
     * Maps given content fields for redis key parts
     *
     * @param $specialKey the key to be overwritten
     * @param array $ezObjectFields
     * @param bool $lookupOldKey indicated if an existing key should be generated
     *
     * @return string|void the overrided value for the redis key
     */
    protected function getRedisKeyStringOverrides($specialKey, array $ezObjectFields, $lookupOldKey = false)
    {

        $key = '';
        switch ($specialKey) {
            case 'tv_channel' :
                if ($lookupOldKey === false) {
                    $channelArray = $ezObjectFields[$specialKey]['content'];
                    $selectedChannelKey = $channelArray['selected_keys'][0];
                    $key = self::$tvChannelMap[$selectedChannelKey];
                } else {
                    $channelArray = $ezObjectFields[$specialKey];
                    $selectedChannelKey = $channelArray['selected_keys'][0];
                    $key = self::$tvChannelMap[$selectedChannelKey];
                }
                break;
            default :
                break;
        };

        return $key;
    }

    /**
     * Creates an array with all information of a format page
     *
     * @return array
     */
    public function render()
    {

        if ($this->getKeyToDelete()) {
            $this->deleteOldKey();
        }

        parent::render();
        $data = array();
        $data['type'] = 'FormatPage';

        $data['title'] = $this->getAttributeValue('title');

        if ($this->getAttributeValue('background_color') == '') {
            $data['backgroundColor'] = 'transparent';
        } else {
            $data['backgroundColor'] = $this->getAttributeValue('background_color');
        }

        $image = array();
        $this->copyFieldContent('background_image', $image, false, array('thumbnail'));
        $data['backgroundImage'] = $image['thumbnail'];

        $data['urlIdentifier'] = $this->getAttributeValue('url_identifier');
        $data['shareTitle'] = $this->getAttributeValue('share_title');
        $data['headerImageTeaserLeftOffset'] = $this->getAttributeValue('header_image_teaser_left_offset');
        $data['headerImageTeaserTopPos'] = $this->getAttributeValue('header_image_teaser_top_pos');

        $activeTabDropDown = $this->getAttributeValue('active_tab');
        $data['activeTab'] = $activeTabDropDown['selected_values'][0];

        $iconDropDownKey = $this->getAttributeValue('icon_tab_module');
        $data['iconTabModule'] = MyVideoicondropdowntype::mapDropDownKeyToChar($iconDropDownKey);

        $data['titleTabModule'] = $this->getAttributeValue('title_tab_module');


        $titles = $this->getAttributeValue('title_tabs');
        $titles = explode(',', $titles);
        $data['titleTabs'] = $titles;

        $channelDropDown = $this->getAttributeValue('tv_channel');
        $selectedKey = $channelDropDown['selected_keys'][0];
        $data['tvChannel'] = self::$tvChannelMap[$selectedKey];

        $data['tvChannelIconChar'] = $this->getIconForChannel($selectedKey);
        //$data['tvChannelLinkout'] = urlencode("/Serien/".$this->getIconForChannel($selectedKey));

        $data['playerSidebarVideoList'] = $this->renderAssetRelationList('player_sidebar_video_list', true);
        $data['postPlayerModules'] = $this->renderAssetRelationList('post_player_modules');
        $data['playerVideoLists'] = $this->renderAssetRelationList('player_video_lists');
        $data['playerSushibars'] = $this->renderAssetRelationList('player_sushibars');
        $data['postVideoListModules'] = $this->renderAssetRelationList('post_video_list_modules');
        $data['headerImageTeaser'] = $this->renderAssetRelationList('header_image_teaser', true);

        $data['simAdConfig'] = $this->renderMyVideoSimAdConfig();
        $data['tracking'] = $this->renderMyVideoTrackingData();
        $data['metaData'] = $this->renderMyVideoMetaData();

        $this->copyFieldContent('banderole', $data);

        return $data;
    }

    /**
     * Maps the selected channel to the matching icon
     *
     * @param $key
     * @return string
     */
    private function getIconForChannel($key)
    {
        $name = self::$tvChannelIconMap[$key];
        return MyVideoIcon::getCharacter($name);
    }
}
