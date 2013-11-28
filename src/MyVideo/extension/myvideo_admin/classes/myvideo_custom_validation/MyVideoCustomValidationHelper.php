<?php

class MyVideoCustomValidationHelper
{
    /**
     * @param ezContentObject $content
     * @param string $attributeName
     *
     * @return eZContentObjectAttribute
     */
    public function getAttribute($content, $attributeName)
    {
        $attributes = $content->contentObjectAttributes();

        /** @var ezContentObjectAttribute $attribute */
        foreach ($attributes as $attribute) {
            if ($attribute != null && $attribute->contentClassAttributeIdentifier() == $attributeName) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     * @param string $attributeName
     *
     * @return eZContentObjectAttribute
     */
    public function getAttributeByNameFromArray($allAttributes, $attributeName)
    {
        foreach ($allAttributes as $attribute) {
            if ($attribute->contentClassAttributeIdentifier() == $attributeName) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * @param eZContentObjectAttribute $attribute
     *
     * @return array
     */
    public function getCommaSeparatedValues($attribute)
    {
        $content = $attribute->content();

        $parts = explode(',', $content);
        $ret = array();
        foreach ($parts as $part) {
            $ret[] = trim($part);
        }

        return $ret;
    }
}
