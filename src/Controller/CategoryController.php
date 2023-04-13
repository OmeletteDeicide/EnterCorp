<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use App\Form\CategoryFormType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    #[Route('/category/form', name: 'app_form_category')]
    public function form(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $getResponse = $form->get("roles_options");
            $roleForCateg = $getResponse->getData();
            $sendRole = array();
            foreach ($roleForCateg as $categ) {
                array_push($sendRole, $categ);
            }
            $category->setAuthorizedroles($sendRole);
            $user = $security->getUser();
            $category->setUser($user);
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute("app_index");
        }


     
        return $this->render('category/form.html.twig', [
            'controller_name' => 'CategoryController',
            'categoryForm' => $form->createView(),
        ]);

       
    }
}
