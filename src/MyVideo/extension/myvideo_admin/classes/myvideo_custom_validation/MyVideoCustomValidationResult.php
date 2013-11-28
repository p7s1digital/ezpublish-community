<?php

class MyVideoCustomValidationResult
{
    const STATUS_OK = 0;
    const STATUS_WARNING = 1;
    const STATUS_ERROR = 2;

    /** @var string */
    private $status;
    /** @var string[] */
    private $facts = array();

    public function __construct($status = self::STATUS_OK, $facts = array())
    {
        $this->status = $status;
        $this->facts = $facts;
    }

    /**
     * @param MyVideoCustomValidationResult $otherResult
     */
    public function merge($otherResult)
    {
        if ($otherResult) {
            $this->status = max($this->status, $otherResult->getStatus());
            $this->facts = array_merge($this->facts, $otherResult->getFacts());
        }
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return $this->status == self::STATUS_OK;
    }

    /**
     * @return bool
     */
    public function isWarning()
    {
        return $this->status == self::STATUS_WARNING;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->status == self::STATUS_ERROR;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFacts()
    {
        return $this->facts;
    }

    /**
     * @param string $fact
     *
     * @return $this
     */
    public function addFact($fact)
    {
        $this->facts[] = $fact;

        return $this;
    }
}
