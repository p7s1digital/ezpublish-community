<?php

class MyVideoMenuValidator extends MyVideoAbstractValidator
{
    const ATTR_START = 'start';
    const ATTR_END = 'end';

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
            case self::ATTR_START:
                return $this->validateStart($allAttributes, $currentAttribute);

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
    public function validateStart($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $endTimeAttribute = $this->helper->getAttributeByNameFromArray($allAttributes, self::ATTR_END);
        // type comes from a "selection" and that is always an array in eZ

        if ($endTimeAttribute->DataInt < $attribute->DataInt) {

            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact('Der Endzeitpunkt liegt vom dem Startzeitpunkt');
        }

        return $result;
    }
}
