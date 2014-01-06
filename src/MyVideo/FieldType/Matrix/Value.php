<?php
/**
 * File containing the Matrix Value class
 */

namespace MyVideo\Converter\Matrix;

use eZ\Publish\Core\FieldType\Value as BaseValue;

/**
 * Value for Null field type
 */
class Value extends BaseValue
{
    /**
     * Matrix content
     *
     * @var mixed[]
     */
    public $matrixValues;

    /**
     * Construct a new Value object and initialize it $selection
     *
     * @param mixed[] $matrixValues
     */
    public function __construct(array $matrixValues = array())
    {
        $this->matrixValues = $matrixValues;
    }

    /**
     * @see \eZ\Publish\Core\FieldType\Value
     */
    public function __toString()
    {
        return implode(',', $this->matrixValues);
    }
}
