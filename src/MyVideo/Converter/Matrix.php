<?php
/**
 * File containing the Matrix converter
 */

namespace MyVideo\Converter;

use DOMDocument;
use eZ\Publish\Core\FieldType\Matrix\Value as MatrixValue;
use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\Core\Persistence\Legacy\EzcDbHandler;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

class Matrix implements Converter
{
    /**
     * Factory for current class
     *
     * @note Class should instead be configured as service if it gains dependencies.
     *
     * @return Matrix
     */
    public static function create()
    {
        return new self;
    }

    /**
     * Converts data from $value to $storageFieldValue
     *
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $value
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue $storageFieldValue
     */
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
        // Not implemented yet.
    }

    /**
     * Converts data from $value to $fieldValue
     *
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue $value
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
     */
    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
        $fieldValue->data = array('matrixValues' => array());
        if ($value->dataText === null) {
            return;
        }

        $dom = new DOMDocument('1.0', 'utf-8');
        $columnCount = null;
        if ($dom->loadXML($value->dataText) === true) {
            foreach ($dom->getElementsByTagName('column') as $column) {
                /** @var \DOMElement $column */
                $fieldValue->data['matrixValues']['columns'][$column->getAttribute('id')] = $column->nodeValue;
            }

            $columnCount = count($fieldValue->data['matrixValues']['columns']);
            $cells = $dom->getElementsByTagName('c');
            $row = 0;

            /*  Value of $value->dataText
                <ezmatrix>
                    <name></name>
                    <columns number="n">
                        <column num="0" id="id_of_column">Column-Value</column>
                        <column num="1" id="id_of_column">Column-Value</column>
                        <column num="2" id="id_of_column">Column-Value</column>
                        ...
                        <column num="n" id="id_of_column">Column-Value</column>
                    </columns>
                    <rows number="n"/>
                    <c>Cell-Value</c>
                    <c>Cell-Value</c>
                    <c>Cell-Value</c>
                    <c>Cell-Value</c>
                    <c>Cell-Value</c>
                    ...
                    <c>Cell-Value</c>
                </ezmatrix>
             */
            for ($cellIndex = 0; $cellIndex < $cells->length; $cellIndex++) {
                // new row
                if ($cellIndex % $columnCount === 0) {
                    $row++;
                }
                // skip empty cells
                if ($cells->item($cellIndex)->nodeValue !== '') {
                    $fieldValue->data['matrixValues']['rows'][$row][] = $cells->item($cellIndex)->nodeValue;
                }
            }
        }
    }

    /**
     * Converts field definition data in $fieldDef into $storageFieldDef
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDef
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition $storageDef
     */
    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
        // Not implemented yet.
    }

    /**
     * Converts field definition data in $storageDef into $fieldDef
     *
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition $storageDef
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDef
     */
    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
        // Not implemented yet..
    }

    /**
     * Returns the name of the index column in the attribute table
     *
     * Returns the name of the index column the datatype uses, which is either
     * "sort_key_int" or "sort_key_string". This column is then used for
     * filtering and sorting for this type.
     *
     * @return string
     */
    public function getIndexColumn()
    {
        return 'sort_key_string';
    }
}
