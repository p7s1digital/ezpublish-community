<?php
/**
 * Render an eZPublish contentType videolist
 */


class MyVideoRenderMyVideoVideolist extends MyVideoRendererBase implements MyVideoRendererInterface
{

    const TYPE_MANUAL = 0;
    const TYPE_CHART = 3;

    /**
     * render simplyfied version
     */
    public function render()
    {
        $default = parent::render();
        $type = array_shift($default['fields']['type']['content']['selected_keys']);

        $data = array();


        switch ($type):
            case (self::TYPE_MANUAL):
                $data['type'] = 'EditorVideoList';
                $data['items'] = $this->renderAssetRelationList('relation_videos');
                break;
            case (self::TYPE_CHART):
                $data['type'] = 'ChartVideoList';
                $data['chartId'] = $default['fields']['chart_id']['content'];
                break;
        endswitch;

        return $data;
    }
}
