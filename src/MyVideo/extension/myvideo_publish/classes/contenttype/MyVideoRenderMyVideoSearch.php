<?php
/**
 * Description of MyVideoRenderMyVideoSearch
 *
 */
class MyVideoRenderMyVideoSearch extends MyVideoRendererBase implements MyVideoRendererInterface
{
    const KEY_PART_IDENTIFIER = 'search';

    /**
     * render content object
     */
    public function render()
    {
        $data = array();
        $data['type'] = 'search';
        $data['tracking'] = $this->renderMyVideoTrackingData();
        $data['metaData'] = $this->renderMyVideoMetaData();
        $data['simAdConfig'] = $this->renderMyVideoSimAdConfig();
        $data['sushibars'] = $this->renderSushibars();
        $data['solrBoost'] = $this->renderSolrBoost();
        $data['suggestions'] = $this->renderSolrQuerySuggestions();

        return $data;
    }

    private function renderSushibars()
    {
        $buffer = array();
        $data = parent::render();
        $arr = $data['fields']['sushibars']['content']['relation_list'];

        foreach ($arr as $item) {
            $itemNode = eZContentObjectTreeNode::fetch($item['node_id']);

            if ($itemNode->IsInvisible || !$this->isContentObjectOnline($itemNode->ContentObject)) {
                continue;
            }

            $renderer = MyVideoRenderFactory::getInstance($itemNode->ContentObject);
            $buffer[] = $renderer->render();
        }

        return $buffer;
    }

    /**
     * Render the related solr boost values
     *
     * @return array
     */
    private function renderSolrBoost()
    {
        $buffer = array();
        $data = parent::render();
        $arr = $data['fields']['solr_boost']['content']['relation_list'];

        foreach ($arr as $item) {
            $itemNode = eZContentObjectTreeNode::fetch($item['node_id']);

            if ($itemNode->IsInvisible || !$this->isContentObjectOnline($itemNode->ContentObject)) {
                continue;
            }

            $renderer = MyVideoRenderFactory::getInstance($itemNode->ContentObject);
            $buffer += $renderer->render();
        }

        return $buffer;
    }

    /**
     * Render the related solr query suggestions
     *
     * @return array
     */
    private function renderSolrQuerySuggestions()
    {
        $standard = parent::render();

        $arr = $standard['fields']['solr_suggestions']['content']['relation_list'];
        $buffer = array();
        foreach ($arr as $item) {
            $itemNode = eZContentObjectTreeNode::fetch($item['node_id']);

            if ($itemNode->IsInvisible || !$this->isContentObjectOnline($itemNode->ContentObject)) {
                continue;
            }

            $renderer = MyVideoRenderFactory::getInstance($itemNode->ContentObject);
            $buffer += $renderer->render();

        }

        return $buffer;
    }

    protected function fetchKeyPartIdentifier()
    {
        return '/' . $this::KEY_PART_IDENTIFIER;
    }
}
