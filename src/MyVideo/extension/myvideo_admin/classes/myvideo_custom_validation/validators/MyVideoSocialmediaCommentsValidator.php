<?php

class MyVideoSocialmediaCommentsValidator extends MyVideoAbstractValidator
{
    const ATTR_SHOW_TWITTER = "show_twitter";
    const ATTR_SHOW_FACEBOOK = "show_facebook";
    const ATTR_HASH_TAG = 'hash_tag';
    const ATTR_TWITTER_WIDGET_ID = 'twitter_widget_id';

    const ERROR_NO_SHOW_CHECKED = 'Es muss mindestens ein SozialMedia Kanal aktiviert sein';
    const ERROR_TWITTER_HASHTAG = 'Für das Twitter Widget wird der HashTag benötigt.';
    const ERROR_TWITTER_WIDGET_ID = 'Für das Twitter Widget wird die Widget ID benötigt.';

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
            case self::ATTR_SHOW_TWITTER:
            case self::ATTR_SHOW_FACEBOOK:
                return $this->validateShow($currentAttribute, $allAttributes);

            case self::ATTR_HASH_TAG:
                return $this->validateTwitterHashTag($currentAttribute, $allAttributes);

            case self::ATTR_TWITTER_WIDGET_ID:
                return $this->validateTwitterWidgetId($currentAttribute, $allAttributes);

            default:
                break;
        } //switch ($attributeName)

        return null;
    }

    /**
     * @param ezContentObjectAttribute $attribute
     * @param ezContentObjectAttribute[] $allAttributes as pointer
     *
     * @return MyVideoCustomValidationResult
     */
    private function validateShow($attribute, &$allAttributes)
    {
        $value = $attribute->content();

        //check if current checkbox is checked
        if($value == false) {
            $showTwitter  = $this->helper->getAttributeByNameFromArray($allAttributes, self::ATTR_SHOW_TWITTER);
            $showFacebook = $this->helper->getAttributeByNameFromArray($allAttributes, self::ATTR_SHOW_FACEBOOK);

            //test if none checkbox is checked
            if($showTwitter->content() == false && $showFacebook->content() == false) {
                return new MyVideoCustomValidationResult(
                    MyVideoCustomValidationResult::STATUS_ERROR,
                    array (
                        self::ERROR_NO_SHOW_CHECKED
                    )
                );
            }
        } //if($value == false)

        return new MyVideoCustomValidationResult(
            MyVideoCustomValidationResult::STATUS_OK
        );
    }

    /**
     * validate widget id if twitter is enable
     *
     * @param ezContentObjectAttribute $attribute
     * @param ezContentObjectAttribute[] $allAttributes as pointer
     *
     * @return MyVideoCustomValidationResult
     */
    private function validateTwitterWidgetId($attribute, &$allAttributes)
    {
        $showTwitter = $this->helper->getAttributeByNameFromArray($allAttributes, self::ATTR_SHOW_TWITTER);

        //test if twitter checkbox is checked
        if($showTwitter->content() == true) {
            $value = $attribute->content();

            if(empty($value) == true) {
                return new MyVideoCustomValidationResult(
                    MyVideoCustomValidationResult::STATUS_ERROR,
                    array (
                        self::ERROR_TWITTER_WIDGET_ID
                    )
                );
            }
        } //if($showTwitter->content() == true)

        return new MyVideoCustomValidationResult(
            MyVideoCustomValidationResult::STATUS_OK
        );
    }

    /**
     * validate hash tag if twitter is enable
     *
     * @param ezContentObjectAttribute $attribute
     * @param ezContentObjectAttribute[] $allAttributes as pointer
     *
     * @return MyVideoCustomValidationResult
     */
    private function validateTwitterHashTag($attribute, &$allAttributes)
    {
        $showTwitter = $this->helper->getAttributeByNameFromArray($allAttributes, self::ATTR_SHOW_TWITTER);

        //test if twitter checkbox is checked
        if($showTwitter->content() == true) {
            $value = $attribute->content();

            if(empty($value) == true) {
                return new MyVideoCustomValidationResult(
                    MyVideoCustomValidationResult::STATUS_ERROR,
                    array (
                        self::ERROR_TWITTER_HASHTAG
                    )
                );
            }
        } //if($showTwitter->content() == true)

        return new MyVideoCustomValidationResult(
            MyVideoCustomValidationResult::STATUS_OK
        );
    }
}
