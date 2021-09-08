<?php

namespace App\Currency;

use App\Entity\Currency;
use Symfony\Component\HttpFoundation\Response;

class AddCurrencies
{
    public function addCurrencies($rates, $entityManager): bool
    {
        foreach ($rates as $rate) {

            $name = $rate->currency;
            $code = $rate->code;
            $value = $rate->mid;

            $new_curr = new Currency();
            $new_curr->setName($name);
            $new_curr->setCode($code);
            $new_curr->setValue($value);
            $new_curr->setLastDate(new \DateTime());

            $entityManager->persist($new_curr);
            $entityManager->flush();

        }

        return true;
    }
}