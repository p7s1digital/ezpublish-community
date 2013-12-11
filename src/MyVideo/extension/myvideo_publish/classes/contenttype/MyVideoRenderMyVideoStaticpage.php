<?php
/**
 * Renders Sub Homepages used at the second level of navigation.
 */

class MyVideoRenderMyVideoStaticpage extends MyVideoRendererBaseDynamicKey
{
    // default is empty, as *no* prefix shall be used
    // url pattern for static_page is: /<url_identifier>
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

        $data['type'] = 'Staticpage';
        $this->copyFieldContent('title', $data);
        $this->copyFieldContent('content', $data);

        $data['simAdConfig'] = $this->renderMyVideoSimAdConfig();
        $data['tracking'] = $this->renderMyVideoTrackingData();
        $data['metaData'] = $this->renderMyVideoMetaData();

        return $data;
    }
}
