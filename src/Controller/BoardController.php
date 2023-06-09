<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Subject;
use App\Entity\Category;
use App\Form\BoardFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use function PHPUnit\Framework\isEmpty;

class BoardController extends AbstractController
{

    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/board/{id}', name: 'app_board')]
    public function index(EntityManagerInterface $entityManager, Security $security, Board $board, Request $request): Response
    {
        $board = $entityManager->getRepository(Board::class)->find($request->attributes->get('id'));

        $subjects = $board->getSubjects();
        if ($subjects->isEmpty()) {
            return $this->redirectToRoute("app_form_subject", ['boardId' => $request->attributes->get('id')]);
        }
        $subjects = array_filter($subjects->toArray(), function (Subject $subjects) {
            $allowedRoles = $subjects->getAuthorizedroles();

            foreach ($allowedRoles as $role) {
                if ($this->security->isGranted($role, $subjects)) {
                    return true;
                }
            }
            return false;
        });
        $category = $board->getCategory();
        return $this->render('board/show.html.twig', [
            'controller_name' => 'BoardController',
            'category' => $category,
            'board' => $board,
            'subjects' => $subjects,
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
    #[Route('/board/delete/{id}', name: 'app_delete_board')]
    public function delete(EntityManagerInterface $entityManager, Security $security, Board $board, Request $request, $id): Response
    {

        $board_deleted = $entityManager->getRepository(Board::class)->find($request->attributes->get('id'));
        $user = $security->getUser();
        $boards_user = $user->getBoards();
        foreach ($boards_user as $board_user) {
            if ($board_deleted->getId() === $board_user->getId()) {
                $category = $board_deleted->getCategory();
                $entityManager->remove($board_deleted);
                $entityManager->flush();
                return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
            }
        }
    }
}
