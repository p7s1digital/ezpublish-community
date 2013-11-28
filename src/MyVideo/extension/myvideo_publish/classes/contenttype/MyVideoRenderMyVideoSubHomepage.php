<?php
/**
 * Renders Sub Homepages used at the second level of navigation.
 */

class MyVideoRenderMyVideoSubHomepage extends MyVideoRendererBaseDynamicKey implements MyVideoRendererInterface
{
    // default is empty, as *no* prefix shall be used
    // url pattern for sub home is: /<url_identifier>
    protected $partIdentifierPrefix = array('default' => '');

    protected $dynamicRedisKeyPartsKeys = array('url_identifier');

    public function render()
    {

        // handle deletion of old redis key
        if ($this->getKeyToDelete()) {
            $this->deleteOldKey();
        }

        parent::render();

        $data = array();

        $data['type'] = 'SubHomepage';
        $this->copyFieldContent('title', $data);
        $this->copyFieldContent('url_identifier', $data);

        $data['simAdConfig'] = $this->renderMyVideoSimAdConfig();
        $data['tracking'] = $this->renderMyVideoTrackingData();
        $data['metaData'] = $this->renderMyVideoMetaData();

        $data['stage'] = $this->renderAssetRelationList('stage', true);
        $data['modules'] = $this->renderAssetRelationList('modules');

        return $data;
    }
}
