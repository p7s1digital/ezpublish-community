<?php
/**
 * File containing the Matrix field type
 */

namespace MyVideo\Converter\Matrix;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;

/**
 * The Matrix field type.
 *
 * This field type represents an array structure.
 */
class Type extends FieldType
{
    /**
     * Returns the field type identifier for this field type
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return "ezmatrix";
    }

    /**
     * Returns the name of the given field value.
     *
     * It will be used to generate content name and url alias if current field is designated
     * to be used in the content name/urlAlias pattern.
     *
     * @param SPIValue $value
     * @return string|void
     * @throws \RuntimeException
     */
    public function getName(SPIValue $value)
    {
        throw new \RuntimeException('@todo Implement this method');
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \eZ\Publish\Core\FieldType\Matrix\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param \eZ\Publish\Core\FieldType\Matrix\Value $inputValue
     *
     * @return \eZ\Publish\Core\FieldType\Matrix\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        if (is_integer($inputValue) || is_string($inputValue)) {
            $inputValue = new Value(array($inputValue));
        } else if (is_array($inputValue)) {
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @param BaseValue $value
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType If the value does not match the expected structure.
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (!is_array($value->matrixValues)) {
            throw new InvalidArgumentType(
                "\$value->matrixValues",
                'array',
                $value->matrixValues
            );
        }

        foreach ($value->matrixValues as $key => $matrixValue) {
            if (!is_integer($matrixValue) && !is_string($matrixValue)) {
                throw new InvalidArgumentType(
                    "\$value->destinationContentIds[$key]",
                    'string|int',
                    $matrixValue
                );
            }
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param BaseValue $value
     * @return mixed|string
     */
    protected function getSortInfo(BaseValue $value)
    {
        return false;
    }

    /**
     * Converts an $hash to the Value defined by the field type
     *
     * @param mixed $hash
     *
     * @return \eZ\Publish\Core\FieldType\Matrix\Value $value
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return new Value($hash['matrixValues']);
    }

    /**
     * Converts a $Value to a hash
     *
     * @param \eZ\Publish\Core\FieldType\Matrix\Value $value
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        return array('matrixValues' => $value->matrixValues);
    }

    /**
     * Returns whether the field type is searchable
     *
     * @return boolean
     */
    public function isSearchable()
    {
        return true;
    }
}
