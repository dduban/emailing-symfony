<?php

namespace App\Emailer;

use App\Repository\AlertRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Alert;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\PropertyAccess\PropertyAccess;


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

            if (!empty($valuesOutRange)) {
                $email = (new TemplatedEmail())
                    ->from('from@example.com')
                    ->to($userEmail)
                    ->subject('teraz zadziala')
                    ->htmlTemplate('emails/confEmail.html.twig')
                    ->context([
                        'valuesOutOfRange' => $valuesOutRange,
                        'userId' => $userId
                    ]);
                $this->mailer->send($email);

            } else {
                return false;
            }
        }

        return true;
    }
}