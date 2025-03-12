<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], array('email' => 'ASC'));
        // remove super admin from list
        $users = array_filter(
            $users,
            fn($user) => $user->getUserIdentifier() !==
                User::SUPER_ADMIN_IDENTIFIER,
        );
        return $this->render('backend/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = new User();
        return $this->proceed($user, $request, $userPasswordHasher, $entityManager);
    }

    #[Route('/edit/{id<\d+>}', name: 'edit')]
    public function edit(
        User $user,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        return $this->proceed($user, $request, $userPasswordHasher, $entityManager);
    }

    /**
     * @param User $user
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    protected function proceed(
        User $user,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('backend/user/form.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
