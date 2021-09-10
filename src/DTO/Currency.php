<?php

namespace App\DTO;

use App\Interfaces\CurrencyDTO;

class Currency implements CurrencyDTO
{

    private $name;
    private $code;
    private $value;

    public function getName()
    {
        return $this->name;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setName($name): static
    {
        $this->name = $name;
        return $this;
    }

    public function setCode($code): static
    {
        $this->code = $code;
        return $this;
    }

    public function setValue($value): static
    {
        $this->value = $value;
        return $this;
    }
}