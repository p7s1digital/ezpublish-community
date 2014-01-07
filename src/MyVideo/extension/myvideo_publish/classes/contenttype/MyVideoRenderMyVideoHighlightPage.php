<?php
/**
 * Renderer for the Highlight Page Class
 */

class MyVideoRenderMyVideoHighlightPage extends MyVideoRendererBaseDynamicKey
{
    const TYPE = 'HighlightPage';

    /**
     * The fixed part of the redis key,
     * mapped by the campaign prefix depending on the locales
     * @var array
     */
    protected $partIdentifierPrefix = array('default' => '');

    /**
     * key parts
     *
     * @var array
     */
    protected $dynamicRedisKeyPartsKeys = array('url');


    /**
     * @return array|null|void
     */
    public function render()
    {
        if ($this->getKeyToDelete()) {
            $this->deleteOldKey();
        }

        $data = array();
        $data['type'] = self::TYPE;
        $data['title'] = $this->getAttributeValue('title');
        $data['url'] = $this->getAttributeValue('url');
        $iconDropDownKey = $this->getAttributeValue('icon_char');
        $data['iconChar'] = MyVideoicondropdowntype::mapDropDownKeyToChar($iconDropDownKey);

        //marked as EditorVideoList
        $this->writeHighlightStage($data);

        $data['sushibar'] = $this->getAttributeValue('sushibar');

        //rss data
        $this->writeRssData($data);

        $data['metaData'] = $this->renderMyVideoMetaData();
        $data['tracking'] = $this->renderMyVideoTrackingData();

        return $data;
    }

    /**
     * write data for hightlightStage alias EditorVideoList
     * @param array $data
     */
    protected function writeHighlightStage(array &$data)
    {
        $data['highlightStage'] = array(
            "type" => "EditorVideoList",
            "items" => $this->renderAssetRelationList('highlight_items')
        );
    }

    /**
     * @param array $data
     */
    protected function writeRssData(array &$data, $key = "rss")
    {
        $title = $this->getAttributeValue("rss_title");
        $name = $this->getAttributeValue("rss_name");
        $rssData = array();

        if (empty($title) == false && empty($name) == false) {
            $rssData = array(
                "title" => $this->getAttributeValue("rss_title"),
                "name" => $this->getAttributeValue("rss_name")
            );
        }

        $data[$key] = $rssData;
    }
}
