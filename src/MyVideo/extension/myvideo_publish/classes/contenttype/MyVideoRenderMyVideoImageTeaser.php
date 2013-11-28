<?php
/**
 * Description of MyVideoRenderMyVideoImageTeaser
 *
 */
class MyVideoRenderMyVideoImageTeaser extends MyVideoRendererBase implements MyVideoRendererInterface
{

    /**
     * render content object
     */
    public function render()
    {
        $data = array();
        $default = parent::render();
        $data['type'] = 'image';

        $this->copyFieldContent('url', $data);
        $this->copyFieldContent('title', $data);
        $this->copyFieldContent('description', $data);
        $this->copyFieldContent('banderole', $data);
        $this->copyFieldContent('is_ad', $data);
        $this->copyFieldContent('is_gradient', $data);
        $this->copyFieldContent('is_premium', $data);
        $this->copyFieldContent('as_no_follow', $data);
        $this->copyFieldContent('image', $data, false, array('src', 'width', 'height'));
        $this->copyFieldContent('cover', $data, false, array('cover'));
        $data['icon'] = MyVideoIcon::generateIconString($default);

        if (isset($default['fields']['tracking_click_pixel_urls'])) {
            $this->copyFieldContent('tracking_click_pixel_urls', $data);
            $data['trackingClickPixelUrls'] = $this->flattenTrackingLinks($data['trackingClickPixelUrls']);
        }

        if (!empty($default['fields']['subtitle']['content'])) {
            $data['subTitle'] = $default['fields']['subtitle']['content'];
        }

        return $data;
    }
}
