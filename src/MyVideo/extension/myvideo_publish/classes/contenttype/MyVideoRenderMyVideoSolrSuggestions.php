<?php
/**
 * Render class for an alias of a Search Word
 * User: christopher queiser
 * Date: 02.10.13
 */

class MyVideoRenderMyVideoSolrSuggestions extends MyVideoRendererBase implements MyVideoRendererInterface
{
    /**
     *
     * @return array
     */
    public function render()
    {
        parent::render();

        $data = array();
        $suggestionList = $this->getAttributeValue('solr_suggestions');
        foreach ($suggestionList as $suggestion) {
            if (strlen($suggestion['col_0']) > 0) {
                $data[$this->getAttributeValue('title')][] = $suggestion['col_0'];
            }
        }
        return $data;
    }
}
