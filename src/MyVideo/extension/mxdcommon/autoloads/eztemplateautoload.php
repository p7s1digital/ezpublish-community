<?php

// Operator autoloading

$eZTemplateOperatorArray = array();

$eZTemplateOperatorArray[] =
    array('script' => 'extension/mxdcommon/autoloads/str_replace_controloperator.php',
        'class' => 'MyStrReplaceOperator',
        'operator_names' => array('ezstr_replace'));

?>