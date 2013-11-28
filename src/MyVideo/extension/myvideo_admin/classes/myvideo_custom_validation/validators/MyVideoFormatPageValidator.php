<?php

class MyVideoFormatPageValidator extends MyVideoAbstractValidator
{
    const ATTR_PLAYER_VIDEO_LISTS = 'player_video_lists';
    const ATTR_PLAYER_SUSHIBARS = 'player_sushibars';
    const ATTR_PLAYERLIST_INDEX = "player_playlist_index";

    const ERROR_PLAYERLIST_INDEX_NOT_INTEGER = "Player Spiellisten Index darf nur eine Ganzzahl über 1 beinhalten";
    const ERROR_NO_SUSHI_OR_VIDEOLIST = 'Es muss ENTWEDER mindestens eine Player-Video-Liste ODER eine Player-Sushibar zugewiesen sein. Momentan ist KEINES von beiden zugewiesen.';
    const ERROR_BOTH_SUSHI_OR_VIDEOLIST = 'Es dürfen nur ENTWEDER Player-Video-Listen ODER Player-Sushibars zugewiesen sein. Momentan ist BEIDES zugewiesen.';

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     * @param ezContentObjectAttribute $currentAttribute
     *
     * @return MyVideoCustomValidationResult
     */
    public function validateAttribute($allAttributes, $currentAttribute)
    {
        $attributeName = $currentAttribute->contentClassAttributeIdentifier();

        switch ($attributeName) {
            case self::ATTR_PLAYERLIST_INDEX:
                return $this->validatePlayerlistIndex($currentAttribute);
            case self::ATTR_PLAYER_VIDEO_LISTS:
                return $this->validatePlayerVideoLists($allAttributes, $currentAttribute);
            case self::ATTR_PLAYER_SUSHIBARS:
                return $this->validatePlayerSushibars($allAttributes, $currentAttribute);
        }

        return null;
    }

    /**
     * @param ezContentObjectAttribute $attribute
     *
     * @return MyVideoCustomValidationResult
     */
    public function validatePlayerlistIndex(&$attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $value = $attribute->content();
        $intValue = intval($value);

        //test the value
        if($intValue < 1 || $value != $intValue) {
            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(
                self::ERROR_PLAYERLIST_INDEX_NOT_INTEGER
            );
        }

        return $result;
    }

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     * @param ezContentObjectAttribute $attribute
     *
     * @return MyVideoCustomValidationResult
     */
    public function validatePlayerVideoLists($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $sushibarsAttr = $this->helper->getAttributeByNameFromArray($allAttributes, self::ATTR_PLAYER_SUSHIBARS);

        $this->validatePlayerVideoListAndPlayerSushibars($result, $attribute, $sushibarsAttr);

        return $result;
    }

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     * @param ezContentObjectAttribute $attribute
     *
     * @return MyVideoCustomValidationResult
     */
    public function validatePlayerSushibars($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $videoListAttr = $this->helper->getAttributeByNameFromArray($allAttributes, self::ATTR_PLAYER_VIDEO_LISTS);

        $this->validatePlayerVideoListAndPlayerSushibars($result, $videoListAttr, $attribute);

        return $result;
    }

    /**
     * @param MyVideoCustomValidationResult $result
     * @param eZContentObjectAttribute $videoListAttr
     * @param eZContentObjectAttribute $sushibarsAttr
     */
    private function validatePlayerVideoListAndPlayerSushibars($result, $videoListAttr, $sushibarsAttr)
    {
        $sushibarsContent = $sushibarsAttr->content();
        $sushibarsRelations = isset($sushibarsContent['relation_list']) ? $sushibarsContent['relation_list'] : array();

        $videoListContent = $videoListAttr->content();
        $videoListRelations = isset($videoListContent['relation_list']) ? $videoListContent['relation_list'] : array();

        if (count($sushibarsRelations) == 0 && count($videoListRelations) == 0) {
            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(
                self::ERROR_NO_SUSHI_OR_VIDEOLIST
            );
        }

        if (count($sushibarsRelations) > 0 && count($videoListRelations) > 0) {
            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(
                self::ERROR_BOTH_SUSHI_OR_VIDEOLIST
            );
        }
    }
}
