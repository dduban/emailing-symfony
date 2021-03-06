<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $msg = $this->confEmail(
                $this->container->getParameter('email_subject'),
                $this->container->getParameter('email_from'),
                $user->getEmail(),
                $user->getAlerts()
            );

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
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

    public function unsubEmail($email)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $emailer = $entityManager
            ->getRepository(UnsubBundle:User)
            ->findOneBy(array('$email'=>$email));

        $entityManager->remove($emailer);
        $entityManager->flush();

        return $this->render('unsubEmail.html.twig');
    }


    public function sendAlert($code, $value)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQuery(
            'SELECT user.email FROM user LEFT JOIN alert ON alert.id_user = user.id LEFT JOIN currency ON alert.currency = currency.id WHERE currency.code='.$code.' AND (alert.min > '.$value.' OR alert.max < '.$value.' )'
        );

    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
