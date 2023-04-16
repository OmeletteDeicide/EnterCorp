<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Message;
use App\Entity\Subject;
use App\Form\MessageFormType;
use App\Form\SubjectFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SubjectController extends AbstractController
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/subject/{Id}', name: 'app_subject')]
    public function index(EntityManagerInterface $entityManager, Security $security, Board $board, Request $request): Response
    {
        $subjects = $board->getSubjects();
        $idBoard = $board->getId();
        if ($subjects->isEmpty()) {
            return $this->redirectToRoute('app_form_subject', ['boardId' => $idBoard]);
        }

        dd($request->attributes->get('Id'));

        $boards = $entityManager->getRepository(Board::class)->find($request->attributes->get('Id'));
        $subjects = $boards->getSubjects()->toArray();
        $subjects = array_filter($subjects, function (Subject $subject) {
            $allowedRoles = $subject->getAuthorizedroles();

            foreach ($allowedRoles as $role) {
                if ($this->security->isGranted($role, $subject)) {
                    return true;
                }
            }
            return false;
        });

        // On affiche les sujets de la catégorie
        return $this->render('subject/show.html.twig', [
            'board' => $board,
            'subjects' => $subjects,
        ]);

    }

    #[Route('/subject/form/{board.Id}', name: 'app_form_subject')]
    public function form(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $boardId = $request->get('boardId');
        $boardRepo = $entityManager->getRepository(Board::class);
        $board = $boardRepo->find($boardId);

        $subject = new Subject();
        $form = $this->createForm(SubjectFormType::class, $subject);

        $subject->setBoard($board);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $getResponse = $form->get("roles_options");

            $dateCreation = new \DateTime();
            $subject->setCreationDate($dateCreation);

            $roleForSubject = $getResponse->getData();
            $sendRole = array("ROLE_ADMIN");
            foreach ($roleForSubject as $subj) {
                array_push($sendRole, $subj);
            }
            $subject->setAuthorizedroles($sendRole);

            $user = $security->getUser();
            $subject->setUser($user);

            $entityManager->persist($subject);
            $entityManager->flush();

            return $this->redirectToRoute('app_subject', );
        }

        return $this->render('subject/form.html.twig', [
            'controller_name' => 'SubjectController',
            'subjectForm' => $form->createView(),
        ]);
    }

    #[Route('/subject/show/{Id}', name: 'app_show_subject')]
    public function show(Subject $subject, Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $message = new Message();
        $messages = $subject->getMessages();
        $idSubject = $subject->getId();

        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dateCreation = new \DateTime();
            $message->setCreationDate($dateCreation);

            $user = $security->getUser();
            $message->setUser($user);

            $entityManager->persist($message);
            // $entityManager->flush();

            return $this->redirectToRoute('app_show_subject', ['Id' => $idSubject]);
        }


        // On affiche les sujets de la catégorie
        return $this->render('subject/show.html.twig', [
            'messages' => $messages,
            'subjects' => $idSubject,
            'messageForm' => $form->createView(),
        ]);
    }


}