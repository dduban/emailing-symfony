<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


/**
 * @Route("/user", methods={"OPTIONS"})
 */
class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/new_rest", name="user_new_rest", methods={"POST"})
     * @throws \Exception
     */
    public function newRest(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        try {
            $email = $data['email'];
            $name = $data['name'];
            $surname = $data['surname'];
            $phone = $data['phone'];
            $birthday = new \DateTime($data['birthday']);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'Sonfing went wrong: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        $isConfirm = 0;

        if (empty($email) || empty($name) || empty($surname) || empty($phone) || empty($birthday)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->userRepository->saveUser($email, $name, $surname, $phone, $birthday, $isConfirm);

        return new JsonResponse(['status' => 'User created'], Response::HTTP_CREATED);

    }


    /**
     * @Route("/rest/{id}", name="user_show_rest", methods={"GET"})
     */
    public function showRest($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'phone' => $user->getPhone(),
            'birthday' => $user->getBirthday(),
            'is_confirm' => $user->getIsConfirm()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/index_rest", name="user_index_rest", methods={"GET"})
     */
    public function indexRest(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'phone' => $user->getPhone(),
                'birthday' => $user->getBirthday(),
                'is_confirm' => $user->getIsConfirm()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/edit_rest/{id}", name="user_update_rest", methods={"PUT"})
     */
    public function updateRest($id, Request $request): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['email']) ? true : $user->setEmail($data['email']);
        empty($data['name']) ? true : $user->setName($data['name']);
        empty($data['surname']) ? true : $user->setSurname($data['surname']);
        empty($data['phone']) ? true : $user->setPhone($data['phone']);
        empty($data['birthday']) ? true : $user->setBirthday($data['birthday']);

        $updatedUser = $this->userRepository->updateUser($user);

        return new JsonResponse($updatedUser->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/delete_rest/{id}", name="user_delete_rest", methods={"DELETE"})
     */
    public function deleteRest($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $this->userRepository->removeUser($user);

        return new JsonResponse(['status' => 'User deleted'], Response::HTTP_OK);
    }


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
    public function new(Request $request): Response
    {
        $user = new User();
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
     * @Route("/{id}/delete", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
