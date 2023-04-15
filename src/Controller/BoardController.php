<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Category;
use App\Form\BoardFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class BoardController extends AbstractController
{
    #[Route('/board', name: 'app_board')]
    public function index(): Response
    {
        return $this->render('board/index.html.twig', [
            'controller_name' => 'BoardController',
        ]);
    }

    #[Route('/board/form/{category.Id}', name: 'app_form_board')]


    public function form(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $categoryId = $request->get('categoryId');
        // On récupère la catégorie correspondante à l'ID
        $categRepo = $entityManager->getRepository(Category::class);
        $category = $categRepo->find($categoryId);
  
        // On crée un nouveau board et on associe la catégorie correspondante
        $board = new Board();
        
        $form = $this->createForm(BoardFormType::class, $board);
        $board->setCategory($category);
        // On crée un formulaire pour ajouter le board

        // On traite la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // ajout des roles 
            $getResponse = $form->get("roles_options");
            $roleForCateg = $getResponse->getData();
            $sendRole = array("ROLE_ADMIN");
            foreach ($roleForCateg as $categ) {
                array_push($sendRole, $categ);
            }
            $board->setAuthorizedroles($sendRole);
            // récupération de l'utilisateur qui créer le board 
            $user = $security->getUser();
            $board->setUser($user);
            // On enregistre le board
            $entityManager->persist($board);
            $entityManager->flush();

            // On redirige l'utilisateur vers la page de la catégorie
            return $this->redirectToRoute('category_show', ['id' => $categoryId]);
        }

        return $this->render('board/form.html.twig', [
            'controller_name' => 'BoardController',
            'boardForm' => $form->createView(),
        ]);
    }
}
