<?php
/**
 * Render an eZPublish contentType myvideo_article
 */

class MyVideoRenderMyVideoArticle extends MyVideoRendererBase implements MyVideoRendererInterface
{

    /**
     * this is a config content type
     */
    const KEY_PART_TYPE = 'config';


    /**
     * render simplyfied version
     *
     */
    public function render()
    {
        $standard = parent::render();
        $data = array();
        $data['type'] = $standard['type'];

        // TODO use helper method
        $data['title'] = $standard['fields']['title']['content'];
        $data['body'] = $standard['fields']['copy']['content'];
        return $data;
    }

    /**
     * loop over the parent nodes until the main footer and render the hole footer
     */
    public function handleDelete($node)
    {
        if ($node instanceof eZContentObjectTreeNode) {
            $mainFooterNode = $this->getMainFooterArticle($node);

            if ($mainFooterNode !== null) {
                $contentObject = $mainFooterNode->object();
                MyVideoRenderer::render($contentObject);
            }
        }
    }

    /**
     * Iterate through the parent nodes to find the main footer and return it
     *
     * @param eZContentObjectTreeNode $node
     * @return eZContentObjectTreeNode
     */
    private function getMainFooterArticle(eZContentObjectTreeNode $node = null)
    {
        if ($node === null) {
            return null;
        }

        if ($node->ClassIdentifier === 'myvideo_footer') {
            return $node;
        }

        $parentNode = eZContentObjectTreeNode::fetch($node->ParentNodeID);
        return $this->getMainFooterArticle($parentNode);
    }
}
