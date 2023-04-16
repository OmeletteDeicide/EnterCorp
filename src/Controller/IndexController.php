<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
<<<<<<< Updated upstream
//modification
=======
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

>>>>>>> Stashed changes
class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController', 'categories' => $categories
        ]);
    }
}
