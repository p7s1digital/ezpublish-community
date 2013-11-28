<?php
require_once(dirname(__FILE__) . '/common.inc.php');
$tpl = eZTemplate::factory();

//get all campaigns based on usergroup
$campaigns = getCampaigns();
// transport the campaign id to the template
if (!empty($campaigns)) {
    $tpl->setVariable('campaigns', $campaigns);
}

// admin area
if ($Params['FunctionName'] == "admin") {
    $tpl->setVariable('admin', true);
    $content = $tpl->fetch('design:myvideo_voting/admin.tpl');
    $path = helperPath(
        array(
            array(
                'url' => false,
                'text' => 'Admin'
            )
        )
    );
} else {
    //active campaign
    $campaignId = $Params['campaignId'];
    if (empty($campaignId) && !empty($campaigns)) {
        $campaignId = $campaigns[0];
    }
    $tpl->setVariable('active', $campaignId);

    $content = $tpl->fetch('design:myvideo_voting/index.tpl');
    $path = helperPath(
        array(
            array(
                'url' => false,
                'text' => ucfirst($campaignId)
            )
        )
    );
}

$Result = array();
$Result['left_menu'] = 'design:myvideo_voting/left_menu.tpl';
$Result['content'] = $content;
$Result['path'] = $path;
