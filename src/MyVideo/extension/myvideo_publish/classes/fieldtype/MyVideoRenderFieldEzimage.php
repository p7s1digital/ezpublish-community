<?php
/**
 * Description of MyVideoRenderFieldEzimage
 *
 */

class MyVideoRenderFieldEzimage extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{
    public function render()
    {
        $contentObjectAttribute = $this->getContentObjectAttribute();
        $targetAliasName = 'original';

        if ($contentObjectAttribute->hasContent()) {
            $aliasHandler = new eZImageAliasHandler($contentObjectAttribute);
            $data = $aliasHandler->imageAlias($targetAliasName);
            $data['src'] = MyVideoCdnStorage::store($data['full_path']);
            $data['thumbnail'] = $data['src'];
            // HINT temp mapping frontend keys ambiguous
            $data['alt'] = $data['alternative_text'];
            $data['title'] = $data['alternative_text'];
            $data['type'] = 'image';
            $data['cover'] = $data['src'];
        } else {
            $data = array();
        }
        return $data;
    }
}
