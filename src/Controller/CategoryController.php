<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Board;
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
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    #[Route('/category', name: 'app_category')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $categories = $entityManager->getRepository(Category::class)->findAll();
        $categories = array_filter($categories, function (Category $category) {
            $allowedRoles = $category->getAuthorizedroles();

            foreach ($allowedRoles as $role) {
                if ($this->security->isGranted($role, $category)) {
                    return true;
                }
            }
            return  false;
        });


        return $this->render('index/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories,
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
            $sendRole = array("ROLE_ADMIN");
            foreach ($roleForCateg as $categ) {
                array_push($sendRole, $categ);
            }
            $category->setAuthorizedroles($sendRole);
            $user = $security->getUser();
            $category->setUser($user);
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute("app_category");
        }



        return $this->render('category/form.html.twig', [
            'controller_name' => 'CategoryController',
            'categoryForm' => $form->createView(),
        ]);
    }
    #[Route('/category/{id}', name: 'category_show')]
    public function show(Category $category): Response
    {

        $boards = $category->getBoards();
        $idCategory = $category->getId();
        if ($boards->isEmpty()) {
            return $this->redirectToRoute('app_form_board', ['categoryId' => $idCategory]);
        }

        // On affiche les sujets de la catégorie
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'boards' => $boards,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'app_delete_category')]
    public function delete(EntityManagerInterface $entityManager, Security $security, Category $board, Request $request, $id): Response
    {
        $category_deleted = $entityManager->getRepository(Category::class)->find($request->attributes->get('id'));
        $user = $security->getUser();
        $categories_user = $user->getCategories();
        foreach ($categories_user as $category_user) {
            if ($category_deleted->getId() === $category_user->getId()) {
                $entityManager->remove($category_deleted);
                $entityManager->flush();
                return $this->redirectToRoute('app_index');
            }
        }
    }
}
