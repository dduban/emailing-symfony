<?php

namespace App\Currency;

use App\Entity\Currency;
use Symfony\Component\HttpFoundation\Response;

class UpdateCurrencies
{

    /**
     * @param \App\DTO\Currency[] $rates
     * @param $entityManager
     * @param $repoCurrency
     * @return bool
     */
    public function updateCurrencies(array $rates, $entityManager, $repoCurrency): bool
    {
        $current = new \DateTime();

        foreach ($rates as $rate) {

            $getCurrency = $repoCurrency->findOneBy(['code' => $rate->getCode()]);

            if(empty($getCurrency)) {

                $new_curr = new Currency();
                $new_curr->setName($rate->getName());
                $new_curr->setCode($rate->getCode());
                $new_curr->setValue($rate->getValue());
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
                        ->setValue($rate->getValue())
                        ->setLastDate($current);
                    $entityManager->persist($getCurrency);
                    $entityManager->flush();
                }
            }
        }

        return true;
    }
}