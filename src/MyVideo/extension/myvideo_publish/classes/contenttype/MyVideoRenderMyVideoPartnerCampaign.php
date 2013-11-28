<?php
/**
 * Render an eZPublish contentType myvideo_partner_campaign
 */
class MyVideoRenderMyVideoPartnerCampaign extends MyVideoRendererBaseDynamicKey
{
    protected $dynamicRedisKeyPartsKeys = array('campaign_url');

    public function render()
    {
        // handle deletion of old redis key
        if ($this->getKeyToDelete()) {
            $this->deleteOldKey();
        }

        $objData = parent::render();
        $data = array();
        $data['type'] = 'FrameEmbed';
        $data['url'] = $objData['fields']['iframe_url']['content'];
        $data['tracking'] = $this->renderMyVideoTrackingData();
        $data['metaData'] = $this->renderMyVideoMetaData();
        $this->copyFieldContent('meta_keywords', $data);
        $this->copyFieldContent('meta_title', $data);
        $this->copyFieldContent('meta_description', $data);
        $this->copyFieldContent('meta_robots', $data);
        return $data;
    }
}
