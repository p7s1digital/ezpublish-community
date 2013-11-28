<?php

class MyVideoCustomValidation
{
    /**
     * @return MyVideoCustomValidation
     */
    public static function getInstance()
    {
        static $obj;

        return $obj ? : $obj = new MyVideoCustomValidation();
    }

    /**
     * @param array $params with [0] => ezContentObject and [1] => the attribute
     *
     * @return string
     */
    public static function validateAttribute($params)
    {
        $object = $params[0];
        $attribute = $params[1];

        return self::getInstance()->validateAttributeInternal($object, $attribute);
    }

    /**
     * @param MyVideoCustomValidationResult $result
     *
     * @return bool
     */
    public static function isOk(MyVideoCustomValidationResult $result)
    {
        return $result->isOk();
    }

    /**
     * @param MyVideoCustomValidationResult $result
     *
     * @return string[]
     */
    public static function getFacts(MyVideoCustomValidationResult $result)
    {
        return $result->getFacts();
    }

    /**
     * @param ezContentObject $content
     * @param ezContentObjectAttribute[] $attributes
     *
     * @return MyVideoCustomValidationResult
     */
    public function validate($content, $attributes)
    {
        $validator = $this->createValidator($content);

        if ($validator) {
            return $validator->validate($attributes);
        }

        return new MyVideoCustomValidationResult();
    }

    /**
    /**
     * @param ezContentObject $object
     * @param ezContentObjectAttribute $attribute
     *
     * @return MyVideoCustomValidationResult
     */
    private function validateAttributeInternal($object, $attribute)
    {
        if ($object !== null && $object->contentClass() !== null) {
            $validator = $this->createValidator($object);

            if ($validator) {

                $result = $validator->validateAttribute($object->contentObjectAttributes(), $attribute);
                if ($result) {
                    return $result;
                }
            }
        }

        return new MyVideoCustomValidationResult(true);
    }

    /**
     * @param ezContentObject $content
     *
     * @return MyVideoCustomValidatorInterface
     */
    private function createValidator($content)
    {
        $className = $this->buildValidatorClassName($content);

        if (class_exists($className)) {
            /** @var MyVideoCustomValidatorInterface $validator */
            $validator = new $className($content);
            return $validator;
        }

        return null;
    }

    /**
     * @param ezContentObject $content
     *
     * @return string
     */
    private function buildValidatorClassName($content)
    {
        $className = MyVideoUtils::toCamelCase($content->contentClass()->Identifier) . 'Validator';
        return str_replace('myvideo', 'MyVideo', $className);
    }
}
