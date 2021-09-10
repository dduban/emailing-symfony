<?php

namespace App\DTO;

use DateTimeInterface;

class AlertData
{
    /**
     * @var string
     */
    private string $code;
    /**
     * @var string
     */
    private string $name;
    /**
     * @var float
     */
    private float $value;
    /**
     * @var float
     */
    private float $min;
    /**
     * @var float
     */
    private float $max;
    /**
     * @var DateTimeInterface
     */
    private DateTimeInterface $lastUpdate;
    /**
     * @var string
     */
    private string $hashId;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return AlertData
     */
    public function setCode(string $code): AlertData
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AlertData
     */
    public function setName(string $name): AlertData
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return AlertData
     */
    public function setValue(float $value): AlertData
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return float
     */
    public function getMin(): float
    {
        return $this->min;
    }

    /**
     * @param float $min
     * @return AlertData
     */
    public function setMin(float $min): AlertData
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return float
     */
    public function getMax(): float
    {
        return $this->max;
    }

    /**
     * @param float $max
     * @return AlertData
     */
    public function setMax(float $max): AlertData
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getLastUpdate(): DateTimeInterface
    {
        return $this->lastUpdate;
    }

    /**
     * @param DateTimeInterface $lastUpdate
     * @return AlertData
     */
    public function setLastUpdate(DateTimeInterface $lastUpdate): AlertData
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    /**
     * @return string
     */
    public function getHashId(): string
    {
        return $this->hashId;
    }

    /**
     * @param string $hashId
     * @return AlertData
     */
    public function setHashId(string $hashId): AlertData
    {
        $this->hashId = $hashId;
        return $this;
    }




}