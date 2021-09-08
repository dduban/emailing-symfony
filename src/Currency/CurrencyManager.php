<?php

namespace App\Currency;


use App\Entity\Alert;
use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;
use App\Emailer\EmailManager;

class CurrencyManager
{
    private DownloadCurrencies $downloadCurrencies;
    private EntityManagerInterface $em;
    private UpdateCurrencies $updateCurrencies;
    private EmailManager $emailManager;

    public function __construct(
        EntityManagerInterface $em,
        DownloadCurrencies     $downloadCurrencies,
        UpdateCurrencies       $updateCurrencies,
        EmailManager           $emailManager
    )
    {
        $this->em = $em;
        $this->downloadCurrencies = $downloadCurrencies;
        $this->updateCurrencies = $updateCurrencies;
        $this->emailManager = $emailManager;
    }

    public function synchronize()
    {
        $repoCurrency = $this->em->getRepository(Currency::class);

        $data = $this->downloadCurrencies->downloadCurrencies();

        $updatedCurrencies = $this->updateCurrencies->updateCurrencies($data, $this->em, $repoCurrency);

        return $this->emailManager->sendAlert();

    }
}