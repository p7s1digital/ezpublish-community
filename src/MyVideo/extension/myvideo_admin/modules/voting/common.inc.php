<?php
function helperPath($arr)
{
    $dummy = array('url' => 'voting', 'text' => 'Voting Service');
    array_unshift($arr, $dummy);

    return $arr;
}

function getCampaigns()
{
    $campaigns = array();

    //get all voting campaign objects
    $list = \eZContentObject::fetchList(
        true,
        array('contentclass_id' => \eZContentClass::classIDByIdentifier('myvideo_voting_campaign'))
    );

    foreach ($list as $item) {
        if ($item->canRead()) {
            $campaigns[] = $item->Name;
        }
    }

    return $campaigns;
}
