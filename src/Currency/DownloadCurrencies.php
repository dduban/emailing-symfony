<?php

namespace App\Currency;



use App\DTO\Currency;
use App\Interfaces\CurrencyDTO;

class DownloadCurrencies
{
    /**
     * @return CurrencyDTO[]
     */
    public function downloadCurrencies(): array
    {
        $session = curl_init();
        curl_setopt($session, CURLOPT_URL, 'https://api.nbp.pl/api/exchangerates/tables/A?format=json');
        curl_setopt($session, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($session);
        curl_close($session);
        $ratesDecode=json_decode($response);

        $currenciesDtoArray = array();

        $rates = $ratesDecode[0]->rates;

        foreach ($rates as $rate) {

            $name = $rate->currency;
            $code = $rate->code;
            $mid = $rate->mid;

            $currency = new Currency();
            $currency->setCode($code);
            $currency->setName($name);
            $currency->setValue($mid);

            $currenciesDtoArray[] = $currency;

        }

        return $currenciesDtoArray;

    }
}