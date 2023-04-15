<?php

namespace App\Controller;

use App\Entity\Subject;
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

    #[Route('/subject', name: 'app_subject')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $subjects = $entityManager->getRepository(Subject::class)->findAll();
        $subjects = array_filter($subjects, function (Subject $subject) {
            $allowedRoles = $subject->getAuthorizedroles();

            foreach ($allowedRoles as $role) {
                if ($this->security->isGranted($role, $subject)) {
                    return true;
                }
            }
            return false;
        });


        return $this->render('subject/index.html.twig', [
            'controller_name' => 'SubjectController',
        ]);
    }

    #[Route('/subject/form', name: 'app_form_subject')]
    public function form(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $boardId = $request->get('boardId');
        $boardRepo = $entityManager->getRepository(Category::class);
        $board = $boardRepo->find($boardId);

        $subject = new Subject();
        $form = $this->createForm(SubjectFormType::class, $subject);

        $subject->setBoard($board);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $getResponse = $form->get("roles_options");

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

            return $this->redirectToRoute('app_subject');
        }

        return $this->render('subject/form.html.twig', [
            'controller_name' => 'SubjectController',
            'subjectForm' => $form->createView(),
        ]);
    }


}