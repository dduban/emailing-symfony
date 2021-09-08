<?php

namespace App\Currency;

use App\Entity\Currency;

class DownloadCurrencies
{
    public function downloadCurrencies()
    {
        $session = curl_init();
        curl_setopt($session, CURLOPT_URL, 'https://api.nbp.pl/api/exchangerates/tables/A?format=json');
        curl_setopt($session, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($session);
        curl_close($session);
        $ratesArray=json_decode($response);


        return $ratesArray[0]->rates;

    }
}