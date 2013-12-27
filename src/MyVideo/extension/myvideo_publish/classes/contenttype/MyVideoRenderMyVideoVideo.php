<?php
/**
 * Render an eZPublish contentType myvideo_video
 */

class MyVideoRenderMyVideoVideo extends MyVideoRendererBase implements MyVideoRendererInterface
{
    /**
     * render simplyfied version
     *
     */
    public function render()
    {
        $standard = parent::render();

        $data = array();
        $data['type'] = 'video';
        $data['id'] = $standard['fields']['video_id']['content'];

        $this->copyFieldContent('image', $data, false, array('thumbnail', 'alt'));
        $this->copyFieldContent('title', $data);
        $this->copyFieldContent('description', $data);
        $this->copyFieldContent('banderole', $data);
        $this->copyFieldContent('is_ad', $data);
        $this->copyFieldContent('cover', $data, false, array('cover'));
        $this->copyFieldContent('hide_title', $data);
        $this->copyFieldContent('hide_playbutton', $data);
        $data['icon'] = MyVideoIcon::generateIconString($standard);

        if (isset($standard['fields']['tracking_click_pixel_urls'])) {
            $this->copyFieldContent('tracking_click_pixel_urls', $data);
            $data['trackingClickPixelUrls'] = $this->flattenTrackingLinks($data['trackingClickPixelUrls']);
        }

        if (!empty($standard['fields']['subtitle']['content'])) {
            $data['subTitle'] = $standard['fields']['subtitle']['content'];
        }

        return $data;
    }
}
