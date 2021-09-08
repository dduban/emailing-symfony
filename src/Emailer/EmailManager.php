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

#    public function sendAlertTest(): bool
#    {
#        $email = (new Email())
#            ->from('alienmailcarrier@example.com')
#            ->to('exampleemail@example.com')
#            ->subject('D-word')
#            ->text("Blagam niech to dziala️");
#        $this->mailer->send($email);

#        return true;
#    }


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
                $email = (new Email())
                    ->from('from@example.com')
                    ->to($userEmail)
                    ->subject('teraz zadziala')
                    ->text('POWINNY BYC DWA');
//                    ->htmlTemplate('emails/alertEmail.html.twig')
//                    ->context([
//                        'valuesOutOfRange' => $valuesOutRange
//                    ]);
                $this->mailer->send($email);

            } else {
                return false;
            }
        }

        return true;
//
//
//        foreach ($valuesOutRange as $value) {
//
//            $userToAlert = $value->getUser();
//
//            if (!empty($valuesOutRange)) {
//                $email = (new Email())
//                    ->from('from@example.com')
//                    ->to($user->getEmail())
//                    ->subject('Currency alert!')
//                    ->htmlTemplate('emails/alertEmail.html.twig')
//                    ->context([
//                        'users' => $alertRepository->findOutOfRangeUser($user->getId())
//                    ]);
//                $this->mailer->send($email);
//            } else {
//                return false;
//            }
//        }
//
//        foreach ($alertsToSend as $alert) {
//            $alert->get
//        }
//        return true;
//
    }
}