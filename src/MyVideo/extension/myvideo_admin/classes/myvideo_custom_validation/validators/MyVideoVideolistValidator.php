<?php

class MyVideoVideolistValidator extends MyVideoAbstractValidator
{
    const ATTR_CHART_ID = 'chart_id';

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
            case self::ATTR_CHART_ID:
                return $this->validateChartId($allAttributes, $currentAttribute);

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
    public function validateChartId($allAttributes, $attribute)
    {
        $result = new MyVideoCustomValidationResult();

        $typeAttribute = $this->helper->getAttributeByNameFromArray($allAttributes, 'type');
        // type comes from a "selection" and that is always an array in eZ
        $type = $typeAttribute->content();
        $type = array_shift($type);

        if ($type == MyVideoRenderMyVideoVideolist::TYPE_CHART) {
            $chartId = $attribute->content();

            if ($chartId < 1) {
                $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
                $result->addFact('Die Chart-ID "' . $chartId . '" ist ungültig');
            }
        }

        return $result;
    }
}
