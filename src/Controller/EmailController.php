<?php

namespace App\Controller;
use App\Entity\Alert;
use App\Form\AlertType;
use App\Repository\AlertRepository;
use App\Repository\UserRepository;
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
    public function unsubscribe(Request $request, Alert $alert): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($alert);
        $entityManager->flush();

        return $this->redirectToRoute('show_alerts', [], Response::HTTP_SEE_OTHER);

    }

    /**
     * @Route("/subscribe/{id}", name="subscribe_alert", methods={"GET", "POST"})
     */
    public function subscribe($id, Request $request, Alert $alert): Response
    {

        $alert = new Alert();
        $form = $this->createForm(AlertType::class, $alert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($alert);
            $entityManager->flush();

            return $this->redirectToRoute('show_alerts', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('alert/new.html.twig', [
            'idUser' => $id,
            'alert' => $alert,
            'form' => $form->createView()
        ]);


    }
}