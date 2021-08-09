<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailerController extends AbstractController
{
    /**
     * @Route("/emailer", name="emailer")
     */
    public function index(): Response
    {
        return $this->render('emailer.html.twig');
    }

    public function new(Request $request): Response
    {
        $user = new User();

        $alert1 = new Alert();
        $alert1->setMin(12);
        $




        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


}
