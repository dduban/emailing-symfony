<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Controller\UserController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class CurrencyController extends AbstractController
{


    public function allCurrencies($lastDate)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $lastupdate = $this->getDoctrine()->getRepository(Currency::class)->find($lastDate);

        if(!$lastDate) {
            throw $this->createNotFoundException(
                'Nie znaleziono rekordow dla danej daty'
            );
        }

        $lastupdate->format('Y-m-d H:i:s');

        $current = new \DateTime();

        $diff = date_diff($lastupdate, $current)->i;

        $currencyTable = [];
        if($diff == 30){

            $session = curl_init();
            curl_setopt($session, CURLOPT_URL, 'https://api.nbp.pl/api/exchangerates/tables/A?format=json');
            curl_setopt($session, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            $response = curl_exec($session);
            curl_close($session);

            $data = json_decode($response);

            foreach ($data[0]->rates as $rate) {

                $name = $rate->currency;
                $code = $rate->code;
                $value = $rate->mid;

                $currencyTable[] = ['name' => $name, 'code' => $code, 'value' => $value];

                $new_curr = new Currency();
                $new_curr->setName($name);
                $new_curr->setCode($code);
                $new_curr->setValue($value);
                $new_curr->setLastDate(new \DateTime());

                $entityManager->persist($new_curr);
                $entityManager->flush();

                sendAlert($code, $value);
            }



        }

    }





}
