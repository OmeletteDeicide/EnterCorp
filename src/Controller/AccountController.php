<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {

        $user = $security->getUser();
        $categories_user = $user->getCategories();


        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'categories_user' => $categories_user,
            'user' => $user
        ]);
    }
}
