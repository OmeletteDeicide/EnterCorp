<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    #[Route('/message/form/{subject.Id}', name: 'app_form_message')]
    public function form(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $subjectId = $request->get('subjectId');
        $subjectRepo = $entityManager->getRepository(Subject::class);
        $subject = $subjectRepo->find($subjectId);

        $dateCreation = date('d/m/Y H:i:s', time());
        $subject->setCreationDate($dateCreation);

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

    #[Route('/message/delete/{id}', name: 'app_delete_message')]
    public function delete(EntityManagerInterface $entityManager, Security $security, Request $request, $id): Response
    {
        $message_deleted = $entityManager->getRepository(Message::class)->find($request->attributes->get('id'));
        $user = $security->getUser();
        $messages_user = $user->getMessages();
        foreach ($messages_user as $message_user) {
            if ($message_deleted->getId() === $message_user->getId()) {
                $subject = $message_deleted->getSubject();
                $entityManager->remove($message_deleted);
                $entityManager->flush();
                return $this->redirectToRoute('app_show_subject', ['id' => $subject->getId()]);
            }
        }
    }
}
