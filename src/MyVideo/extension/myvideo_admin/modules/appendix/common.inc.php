<?php
function helperPath($arr)
{
    $dummy = array('url' => 'appendix', 'text' => 'Appendix');
    array_unshift($arr, $dummy);
    return $arr;
}
