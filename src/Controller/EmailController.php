<?php

namespace App\Controller;
use App\Repository\AlertRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class EmailController extends AbstractController
{



    public function sendAlert(AlertRepository $alertRepository, MailerInterface $mailer, UserRepository $userRepository): Response
    {

        foreach ($userRepository as $user) {

            $email = (new Email())
                ->from('')
                ->to()
                ->subject('Currency alert!')
                ->htmlTemplate('emails/alertEmail.html.twig')
                ->context([
                    'alerts' => $alertRepository->findAll()
                ]);

            return $mailer->send($email);

        }

    }






    public function confEmail($subject, $sender_address, $recipient_address, $alerts)
    {

        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($sender_address)
            ->setTo($recipient_address)
            ->setBody(
                $this->renderView(
                    'confEmail.html.twig',
                    array('alerts' => $alerts)),
                'text/html'
            );

        return $this->get('mailer')->send($message);

    }
}