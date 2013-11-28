<?php
/**
 * Description of MyVideoRenderFieldEztext
 *
 */
class MyVideoRenderFieldEzmatrix extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    public function render()
    {
        /** @var eZContentObjectAttribute $att */
        $att = $this->getContentObjectAttribute();

        $matrix = new eZMatrix('att');
        $matrix->decodeXML($att->attribute('data_text'));

        $rows = $matrix->attribute('rows');

        $ret = array();

        foreach ($rows['sequential'] as $row) {

            $current = array();
            foreach ($row['columns'] as $idx => $value) {

                $column = $matrix->column($idx);
                $current[$column['columnDefinition']['identifier']] = $value;
            }
            $ret[] = $current;
        }

        return $ret;
    }
}
