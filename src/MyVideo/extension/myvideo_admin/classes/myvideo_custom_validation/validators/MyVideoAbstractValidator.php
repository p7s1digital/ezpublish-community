<?php

abstract class MyVideoAbstractValidator implements MyVideoCustomValidatorInterface
{
    /** @var ezContentObject */
    protected $contentObject;
    /** @var MyVideoCustomValidationHelper */
    protected $helper;

    /**
     * @param ezContentObject $content
     */
    public function __construct($content)
    {
        $this->contentObject = $content;
        $this->helper = new MyVideoCustomValidationHelper();
    }

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     *
     * @return MyVideoCustomValidationResult|void
     */
    public function validate($allAttributes)
    {
        $result = new MyVideoCustomValidationResult();

        foreach ($allAttributes as $attribute) {
            $partialResult = $this->validateAttribute($allAttributes, $attribute);
            $result->merge($partialResult);
        }

        return $result;
    }

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     * @param string $attributeName
     *
     * @return eZContentObjectAttribute
     */
    protected function getAttribute($allAttributes, $attributeName)
    {
        return $this->helper->getAttributeByNameFromArray($allAttributes, $attributeName);
    }
}
