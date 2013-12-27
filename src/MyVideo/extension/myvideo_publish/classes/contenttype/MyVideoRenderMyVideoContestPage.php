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

        $selectedValues = $this->getAttributeValue('contest_type');
        $data['contestType'] = $selectedValues['selected_values'][0];

        $this->copyFieldContent('title', $data);
        $this->copyFieldContent('share_title', $data);
        $this->copyFieldContent('page_headline_big', $data);
        $this->copyFieldContent('page_headline_small', $data);

        $this->copyFieldContent('upload_button_label', $data);

        $data = $this->renderHeaderTeaser($data);

        $this->copyFieldContent('campaign_url', $data);
        $this->copyFieldContent('voting_id', $data);
        $this->copyFieldContent('user_id', $data);
        $this->copyFieldContent('group_id', $data);

        $selectedValues = $this->getAttributeValue('vote_asset_source_latest');
        $data['voteAssetSourceLatest'] = $selectedValues['selected_values'][0];

        $selectedValues = $this->getAttributeValue('vote_asset_source_top');
        $data['voteAssetSourceTop'] = $selectedValues['selected_values'][0];

        $this->copyFieldContent('vote_asset_list', $data);

        $this->copyFieldContent('upload_service', $data);

        $selectedValues = $this->getAttributeValue('upload_service');
        $data['uploadService'] = $selectedValues['selected_values'][0];

        $this->copyFieldContent('myvideo_upload_url', $data);
        $this->copyFieldContent('filepicker_key', $data);

        $this->copyFieldContent('hide_intro', $data);
        $this->copyFieldContent('disable_upload', $data);
        $this->copyFieldContent('hide_voting_assets', $data);

        $titles = trim($this->getAttributeValue('tab_label'));
        if (!empty($titles)) {
            $titles = explode(',', $titles);
        } else {
            $titles = array();
        }
        $data['tabLabel'] = $titles;

        $data['postPlayerModules'] = $this->renderAssetRelationList('post_player_modules');
        $data['postIntroModules'] = $this->renderAssetRelationList('post_intro_modules');
        $data['postSushibarsModules'] = $this->renderAssetRelationList('post_sushibars_modules');

        $this->copyFieldContent('terms_and_conditions', $data);

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

        $image = array();
        $this->copyFieldContent('intro_icon', $image, false, array('thumbnail'));
        $data['introIcon'] = $image['thumbnail'];
        $this->copyFieldContent('intro_icon_label', $data);


        $this->copyFieldContent('custom_template', $data);
        $this->copyFieldContent('custom_style', $data);
        $this->copyFieldContent('custom_script', $data);

        $this->copyFieldContent('banderole', $data);

        return $data;
    }

    /**
     * @return array
     */
    public function renderHeaderTeaser($data)
    {
        $teaserData = array();
        $teaserData['type'] = 'HeaderTeaser';

        $teaserData['left'] = $this->getAttributeValue('header_image_teaser_left_offset');
        $teaserData['top'] = $this->getAttributeValue('header_image_teaser_top_offset');

        $teaser = array();
        $this->copyFieldContent('header_image_teaser', $teaser);
        $teaserData['teaser'] = $teaser['headerImageTeaser'];

        if ($teaserData['teaser']) {
            $data['HeaderTeaser'] = $teaserData;
        }

        return $data;
    }

}
