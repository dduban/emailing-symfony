<?php

namespace App\Currency;

use App\Entity\Currency;
use Symfony\Component\HttpFoundation\Response;

class UpdateCurrencies
{
    public function updateCurrencies($rates, $entityManager, $repoCurrency): bool
    {
        $current = new \DateTime();

        foreach ($rates as $rate) {

            $name = $rate->currency;
            $code = $rate->code;
            $value = $rate->mid;

            $getCurrency = $repoCurrency->findOneBy(['code' => $code]);

            if(empty($getCurrency)) {

                $new_curr = new Currency();
                $new_curr->setName($name);
                $new_curr->setCode($code);
                $new_curr->setValue($value);
                $new_curr->setLastDate(new \DateTime());

                $entityManager->persist($new_curr);
                $entityManager->flush();

            }
            else {
                $getLastDate = $getCurrency->getLastDate();

                $currentTimestamp = date_timestamp_get($current);
                $getLastDateTimestamp = date_timestamp_get($getLastDate);

                $diff = $currentTimestamp - $getLastDateTimestamp;
                $diff = $diff / 60;

                if ($diff >= 30) {
                    $getCurrency
                        ->setValue($value)
                        ->setLastDate($current);
                    $entityManager->persist($getCurrency);
                    $entityManager->flush();
                }
            }
        }

        return true;
    }
}