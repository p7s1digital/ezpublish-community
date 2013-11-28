<?php

/**
 * Render an eZPublish contentType SocialMedia Comments
 */
class MyVideoRenderMyVideoSocialmediaComments extends MyVideoRendererBase implements MyVideoRendererInterface
{
    const TYPE = "SocialmediaComments";

    public function render()
    {
        parent::render();

        $data = array();
        $data['type'] = self::TYPE;
        $this->copyFieldContent('show_facebook', $data);
        $this->copyFieldContent('show_twitter', $data);

        //twitter parameter
        $this->copyFieldContent('hash_tag', $data);
        $this->copyFieldContent('twitter_widget_id', $data);

        return $data;
    }
}
