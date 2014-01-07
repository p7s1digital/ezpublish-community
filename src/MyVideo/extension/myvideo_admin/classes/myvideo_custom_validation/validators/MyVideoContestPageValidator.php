<?php

class MyVideoContestPageValidator extends MyVideoAbstractValidator
{
    const ATTR_CONTEST_SOURCE_TOP = 'vote_asset_source_top';
    const ATTR_CONTEST_TYPE = 'contest_type';
    const ATTR_CONTEST_UPLOAD_SERVICE = 'upload_service';
    const ATTR_CONTEST_FILEPICKER_KEY = 'filepicker_key';
    const ATTR_CONTEST_MYVIDEO_UPLOAD_URL = 'myvideo_upload_url';
    const ATTR_CONTEST_VOTING_ID = 'voting_id';
    const ATTR_CONTEST_VOTE_ASSET_LIST = 'vote_asset_list';

    const CONTEST_TYPE_ONLY_VOTING  = 'onlyVoting';
    const CONTEST_TYPE_ONLY_UPLOAD  = 'onlyUpload';
    const CONTEST_TYPE_VOTING_AND_UPLOAD  = 'votingAndUpload';

    const CONTEST_TOP_SOURCE_VOTING_SERVICE  = 'voting_service';

    const CONTEST_UPLOAD_SERVICE_FILEPICKER  = 'filepicker';
    const CONTEST_UPLOAD_SERVICE_MYVIDEO  = 'myvideo';

    const ERROR_INVALID_TOP_SOURCE = "Wenn als Content Type votingAndUpload gewählt wurde, muss die Quelle für Top Videos voting_service sein.";
    const ERROR_EMPTY_UPLOAD_SERVICE = "Upload Service Auswahl darf nicht leer sein, wenn der Contest Type onlyUpload oder votingAndUpload ist.";
    const ERROR_FILEPICKER_KEY = "Upload Service Auswahl ist Filepicker, aber es wurde kein Filepicker Key angegeben.";
    const ERROR_MYVIDEO_UPLOAD_URL = "Upload Service Auswahl ist MyVideo, aber es wurde keine MyVideo Upload URL angegeben.";
    const ERROR_NO_VIDEO_LIST = "Es muss eine Videoliste gepflegt sein, wenn als Quelle für Top Videos 'videolist' asugewählt worden ist.";
    const ERROR_NO_VIDEO_LIST_WRONG_TYPE = "Es wurde ein falscher Objecttyp gepflegt. Es darf nur der Typ videolist verwendet werden.";
    const ERROR_NO_VOTING_ID = "Eine Voting ID muß vorhanden sein, wenn der Contest Type onlyUpload oder votingAndUpload ist.";

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
            case self::ATTR_CONTEST_SOURCE_TOP:
                return $this->validateAssetSourceTop($allAttributes, $currentAttribute);
            case self::ATTR_CONTEST_UPLOAD_SERVICE:
                return $this->validateUploadService($allAttributes, $currentAttribute);
            case self::ATTR_CONTEST_FILEPICKER_KEY:
                return $this->validateFilePicker($allAttributes, $currentAttribute);
            case self::ATTR_CONTEST_MYVIDEO_UPLOAD_URL:
                return $this->validateMyVideoUpload($allAttributes, $currentAttribute);
            case self::ATTR_CONTEST_VOTE_ASSET_LIST:
                return $this->validateVoteAssesList($allAttributes, $currentAttribute);
            case self::ATTR_CONTEST_VOTING_ID:
                return $this->validateVotingId($allAttributes, $currentAttribute);
        }

        return null;
    }

    public function validateAssetSourceTop($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $selectedTopSource = $attribute->title();
        $contestTypeAttr = $this->getAttribute($allAttributes, self::ATTR_CONTEST_TYPE);
        $selectedType = $contestTypeAttr->title();

        if ($selectedType === self::CONTEST_TYPE_VOTING_AND_UPLOAD && $selectedTopSource !== self::CONTEST_TOP_SOURCE_VOTING_SERVICE)
        {
            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(
                self::ERROR_INVALID_TOP_SOURCE
            );
        }

        return $result;

    }

    public function validateUploadService($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $selectedUploadService = $attribute->title();

        $contestTypeAttr = $this->getAttribute($allAttributes, self::ATTR_CONTEST_TYPE);
        $selectedType = $contestTypeAttr->title();

        if (($selectedType === self::CONTEST_TYPE_VOTING_AND_UPLOAD || $selectedType === self::CONTEST_TYPE_ONLY_UPLOAD)
            && empty($selectedUploadService)) {

            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(
                self::ERROR_EMPTY_UPLOAD_SERVICE
            );

        }

        return $result;

    }

    public function validateFilepicker($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $filepickerKey = $attribute->content();

        $contestTypeAttr = $this->getAttribute($allAttributes, self::ATTR_CONTEST_UPLOAD_SERVICE);
        $selectedUploadService = $contestTypeAttr->title();

        if (empty($filepickerKey) && $selectedUploadService === 'filepicker') {
            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(
                self::ERROR_FILEPICKER_KEY
            );
        }
        return $result;

    }

    public function validateMyVideoUpload($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $filepickerKey = $attribute->content();

        $contestTypeAttr = $this->getAttribute($allAttributes, self::ATTR_CONTEST_UPLOAD_SERVICE);
        $selectedUploadService = $contestTypeAttr->title();

        if (empty($filepickerKey) && $selectedUploadService === 'myvideo') {
            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(
                self::ERROR_MYVIDEO_UPLOAD_URL
            );
        }
        return $result;

    }

    public function validateVoteAssesList($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $videoList = $attribute->content();

        $contestTypeAttr = $this->getAttribute($allAttributes, self::ATTR_CONTEST_SOURCE_TOP);
        $selectedUploadService = $contestTypeAttr->title();

        if (!empty($videoList) && $videoList->ClassIdentifier !== 'myvideo_videolist') {
            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(
                self::ERROR_NO_VIDEO_LIST_WRONG_TYPE
            );
        }

        if (empty($videoList)&& $selectedUploadService === 'videolist') {
            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(
                self::ERROR_NO_VIDEO_LIST
            );
        }
        return $result;

    }

    public function validateVotingId($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $votingId = $attribute->content();

        $contestTypeAttr = $this->getAttribute($allAttributes, self::ATTR_CONTEST_TYPE);
        $selectedType = $contestTypeAttr->title();

        if (($selectedType === self::CONTEST_TYPE_VOTING_AND_UPLOAD || $selectedType === self::CONTEST_TYPE_ONLY_UPLOAD)
            && empty($votingId)) {

            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(
                self::ERROR_NO_VOTING_ID
            );

        }

        return $result;

    }

}
