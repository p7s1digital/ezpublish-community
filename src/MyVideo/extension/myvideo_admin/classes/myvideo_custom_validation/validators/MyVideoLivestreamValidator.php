<?php

class MyVideoLivestreamValidator extends MyVideoAbstractValidator
{
    const ATTR_START_TIME = 'start_time';
    const ATTR_END_TIME = 'end_time';
    const ATTR_PUBLISHING_TIME = 'publishing_time';

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
            case self::ATTR_START_TIME:
                return $this->validateStartTime($allAttributes, $currentAttribute);
            case self::ATTR_PUBLISHING_TIME:
                return $this->validatePublishingTime($allAttributes, $currentAttribute);

            default:
                break;
        }

        return null;
    }

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     * @param ezContentObjectAttribute $attribute
     *
     * @return MyVideoCustomValidationResult
     */
    public function validateStartTime($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $endTimeAttribute = $this->helper->getAttributeByNameFromArray($allAttributes, self::ATTR_END_TIME);
        // type comes from a "selection" and that is always an array in eZ

        if ($endTimeAttribute->DataInt < $attribute->DataInt) {

            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact('Der Endzeitpunkt liegt vom dem Startzeitpunkt');
        }

        return $result;
    }

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     * @param ezContentObjectAttribute $attribute
     *
     * @return MyVideoCustomValidationResult
     */
    public function validatePublishingTime($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $startTimeAttribute = $this->helper->getAttributeByNameFromArray($allAttributes, self::ATTR_START_TIME);
        // type comes from a "selection" and that is always an array in eZ

        if ($startTimeAttribute->DataInt < $attribute->DataInt) {

            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact('Der Startzeitpunkt liegt vom dem Veröffentlichungszeitpunkt');
        }

        return $result;
    }
}
