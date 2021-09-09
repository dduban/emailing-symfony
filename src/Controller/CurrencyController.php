<?php

namespace App\Controller;

use App\Currency\AddCurrencies;
use App\Currency\UpdateCurrencies;
use App\Entity\Currency;
use App\Entity\Alert;
use App\Controller\UserController;
use App\Currency\DownloadCurrencies;
use App\Repository\AlertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CurrencyController extends AbstractController
{

    /**
     * @Route("/getcurrency/", name="get_currency", methods={"GET","POST"})
     */
    public function allCurrencies(DownloadCurrencies $downloadCurrencies, UpdateCurrencies $updateCurrencies, AddCurrencies $addCurrencies)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Currency::class);
        $currencies = $repo->findAll();

        $data = $downloadCurrencies->downloadCurrencies();

        if (empty($currencies)) {
            return $addCurrencies->addCurrencies($data, $entityManager);
        } else {
            return $updateCurrencies->updateCurrencies($data, $entityManager, $repo);
        }


    }

    /**
     * @Route("/alerts/", name="show_alerts", methods={"GET","POST"})
     */
    public function index(AlertRepository $alertRepository): Response
    {
        $alerts = $alertRepository->findOutOfRange(3);


        return $this->render('user/indexalert.html.twig', [
            'alerts' => $alerts,
        ]);
    }


}
