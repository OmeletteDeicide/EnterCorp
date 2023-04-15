<?php

namespace App\Controller;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    #[Route('/message/form', name: 'app_form_message')]
    public function form(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $subjectId = $request->get('subjectId');
        $subjectRepo = $entityManager->getRepository(Subject::class);
        $subject = $subjectRepo->find($subjectId);

        $message = new Message();
        $form = $this->createForm(SubjectFormType::class, $message);

        $message->setSubject($subject);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $security->getUser();
            $message->setUser($user);

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_subject');
        }

        return $this->render('message/form.html.twig', [
            'controller_name' => 'MessageController',
            'messageForm' => $form->createView(),
        ]);
    }
}