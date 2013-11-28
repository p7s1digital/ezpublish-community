<?php

class MyVideoVideoValidator extends MyVideoAbstractValidator
{
    const ASPECT_RATIO_DVD_COVER = 0.71;
    const ASPECT_RATIO_TOLERANCE_DVD_COVER = 0.1;

    const ATTR_COVER = 'cover';

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
            case self::ATTR_COVER:
                return $this->validateCover($allAttributes, $currentAttribute);

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
    public function validateCover($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $httpFileName = "ContentObjectAttribute_data_imagename_" . $attribute->ID;

        if (isset($_FILES[$httpFileName]) and  $_FILES[$httpFileName]["tmp_name"] != "") {
            $imageFile = $_FILES[$httpFileName]['tmp_name'];
            list($imgWidth, $imgHeight) = getimagesize($imageFile);
            $givenAspectRatio = $imgWidth / $imgHeight;

            if (abs(self::ASPECT_RATIO_DVD_COVER - $givenAspectRatio) > self::ASPECT_RATIO_TOLERANCE_DVD_COVER) {
                $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
                $result->addFact('Das Bild-Format entspricht nicht einem DVD Cover. Erwartetes Seitenverhältnis ist: ' . self::ASPECT_RATIO_DVD_COVER . '.');
            }

        }

        return $result;
    }
}
