<?php

namespace App\Interfaces;

interface CurrencyDTO
{
    public function getName();
    public function getCode();
    public function getValue();

    public function setName($name);
    public function setCode($code);
    public function setValue($value);
}