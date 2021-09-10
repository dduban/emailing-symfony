<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Entity\User;
use App\Form\AlertType;
use App\Repository\AlertRepository;
use App\Repository\UserRepository;
use App\Tools\UrlHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class EmailController extends AbstractController
{


    /**
     * @Route("/unsubscribe/{id}", name="unsubscribe_alert", methods={"GET", "POST"})
     */
    public function unsubscribe(Request $request): Response
    {
        $alertId = $this->base64url_decode($request->get('id'));

        $entityManager = $this->getDoctrine()->getManager();
        $alertObject = $entityManager->getRepository(Alert::class)->find($alertId);

        $entityManager->remove($alertObject);
        $entityManager->flush();

        return $this->redirectToRoute('show_alerts', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/subscribe/{id}", name="subscribe_alert", methods={"GET", "POST"})
     */
    public function subscribe(Request $request): Response
    {

        $alert = new Alert();
        $userId = (int)$this->base64url_decode($request->get('id'));
        $entityManager = $this->getDoctrine()->getManager();
        $userObject = $entityManager->getRepository(User::class)->find($userId);

        $alert->setIdUser($userObject);
        $form = $this->createForm(AlertType::class, $alert);
        $form->get('idUser')->setData($userId);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('show_alerts', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('alert/new.html.twig', [
            'userId' => $userId,
            'alert' => $alert,
            'form' => $form->createView()
        ]);

    }


    private function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}