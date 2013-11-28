<?php
/**
 * Render an eZPublish contentType myvideo_partner_campaign
 */
class MyVideoRenderMyVideoContestPage extends MyVideoRendererBaseDynamicKey
{
    protected $partIdentifierPrefix =
        array(
            'default' => 'Campaign/',
            'ger-DE' => 'Aktion/'
        );

    protected $dynamicRedisKeyPartsKeys = array('campaign_url');

    public function render()
    {
        // handle deletion of old redis key
        if ($this->getKeyToDelete()) {
            $this->deleteOldKey();
        }

        $objData = parent::render();
        $data = array();
        $data['type'] = 'contest';
        $this->copyFieldContent('campaign_url', $data);
        $this->copyFieldContent('voting_id', $data);
        $this->copyFieldContent('user_id', $data);

        $image = array();
        $this->copyFieldContent('background_image', $image, false, array('thumbnail'));

        $data['backgroundImage'] = $image['thumbnail'];
        $data['tracking'] = $this->renderMyVideoTrackingData();
        $data['metaData'] = $this->renderMyVideoMetaData();
        $data['sushibars'] = $this->renderMyVideoSushibars();

        $this->copyFieldContent('headline', $data);
        $this->copyFieldContent('headline_extra1', $data);
        $this->copyFieldContent('headline_extra2', $data);
        $this->copyFieldContent('headline_extra3', $data);
        $this->copyFieldContent('headline_extra4', $data);

        $this->copyFieldContent('text_paragraph', $data);
        $this->copyFieldContent('text_paragraph_extra1', $data);
        $this->copyFieldContent('text_paragraph_extra2', $data);
        $this->copyFieldContent('text_paragraph_extra3', $data);
        $this->copyFieldContent('text_paragraph_extra4', $data);

        $this->copyFieldContent('banderole', $data);

        return $data;
    }
}
