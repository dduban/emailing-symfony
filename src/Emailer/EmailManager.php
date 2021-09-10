<?php

namespace App\Emailer;

use App\DTO\AlertData;
use App\Repository\AlertRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Alert;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Tools\UrlHelper;


class EmailManager
{
    private AlertRepository $alertRepository;
    private MailerInterface $mailer;
    private UserRepository $userRepository;
    private EntityManagerInterface $em;


    public function __construct(
        EntityManagerInterface $em,
        AlertRepository        $alertRepository,
        MailerInterface        $mailer,
        UserRepository         $userRepository,
    )
    {
        $this->alertRepository = $alertRepository;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }


    public function sendAlert(): bool
    {
        $repoAlerts = $this->em->getRepository(Alert::class);
        $valuesOutRangeUsers = $repoAlerts->findOutOfRangeUsers();

        $propertyAccessor = PropertyAccess::createPropertyAccessor();


        foreach ($valuesOutRangeUsers as $user) {

            $userId = $propertyAccessor->getValue($user, '[userId]');
            $userEmail = $propertyAccessor->getValue($user, '[userEmail]');
            $valuesOutRange = $repoAlerts->findOutOfRange($userId);

            $userIdHash = $this->base64url_encode($userId);

            $valuesToSend = [];

            foreach ($valuesOutRange as $alert) {
                $alertData = new AlertData();
                $alertData->setValue($alert->getCurrency()->getValue())
                    ->setName($alert->getCurrency()->getName())
                    ->setCode($alert->getCurrency()->getCode())
                    ->setMax($alert->getMax())
                    ->setMin($alert->getMin())
                    ->setLastUpdate($alert->getCurrency()->getLastDate())
                    ->setHashId($this->base64url_encode($alert->getId()));
                $valuesToSend[] = $alertData;
            }


            if (!empty($valuesOutRange)) {
                $email = (new TemplatedEmail())
                    ->from('from@example.com')
                    ->to($userEmail)
                    ->subject('pjonteczek')
                    ->htmlTemplate('emails/confEmail.html.twig')
                    ->context([
                        'valuesOutOfRange' => $valuesOutRange,
                        'userIdHash' => $userIdHash,
                        'valuesToSend' => $valuesToSend
                    ]);
                $this->mailer->send($email);

            } else {
                return false;
            }
        }

        return true;
    }

    function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

}