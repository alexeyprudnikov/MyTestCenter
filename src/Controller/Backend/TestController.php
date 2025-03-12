<?php

namespace App\Controller\Backend;

use App\Entity\Test;
use App\Entity\User;
use App\Form\TestType;
use App\Service\TestService;
use App\Utils\Strings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/test', name: 'test_')]
class TestController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'list')]
    public function index(
        #[CurrentUser] ?User $user
    ): Response
    {
        return $this->render('backend/test/index.html.twig', [
            'tests' => $user->getTests()
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(
        #[CurrentUser] ?User $user,
        Request $request
    ): Response
    {
        $test = new Test();
        $test
            ->setHash(Strings::hash())
            ->setUser($user);

        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($test);
            $this->entityManager->flush();

            return $this->redirectToRoute('test_list');
        }

        return $this->render('backend/test/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/view/{id<\d+>}', name: 'view')]
    #[IsGranted("view", subject: "test")]
    public function view(
        Test $test
    ): Response
    {
        // todo: allow only for current user
        return $this->render('backend/test/form.html.twig', [
            'test' => $test,
        ]);
    }

    #[Route('/edit/{id<\d+>}', name: 'edit')]
    #[IsGranted("edit", subject: "test")]
    public function edit(
        Test $test,
        TestService $testService,
        Request $request
    ): Response
    {
        // todo: allow only for current user
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($test);
            $this->entityManager->flush();

            return $this->redirectToRoute('test_list');
        }

        return $this->render('backend/test/form.html.twig', [
            'form' => $form,
            'test' => $test,
        ]);
    }
}
