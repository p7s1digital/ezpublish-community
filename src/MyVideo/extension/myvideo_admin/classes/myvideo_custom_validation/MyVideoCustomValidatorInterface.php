<?php

interface MyVideoCustomValidatorInterface
{
    /**
     * @param ezContentObjectAttribute[] $allAttributes
     *
     * @return MyVideoCustomValidationResult|void
     */
    public function validate($allAttributes);

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     * @param ezContentObjectAttribute $currentAttribute
     *
     * @return MyVideoCustomValidationResult
     */
    public function validateAttribute($allAttributes, $currentAttribute);
}
