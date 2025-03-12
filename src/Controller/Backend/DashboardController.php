<?php

namespace App\Controller\Backend;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(
        #[CurrentUser] ?User $user
    ): Response
    {
        return $this->render('backend/dashboard/index.html.twig', [
            'user' => $user,
        ]);
    }
}
