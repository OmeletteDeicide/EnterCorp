<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $checkEmail = $user->getEmail();
            if(str_ends_with($checkEmail, '@insider.fr')){

                $user->setRoles(["ROLE_INSIDER"]);
            }elseif(str_ends_with($checkEmail, '@collaborator.fr')){
                $user->setRoles(["ROLE_COLLABORATOR"]);
            }elseif(str_ends_with($checkEmail, '@external.fr')){
                $user->setRoles(["ROLE_INSIDER"]);
            }
            switch($checkEmail){
                case str_ends_with($checkEmail, '@insider.fr'):
                    $user->setRoles(["ROLE_INSIDER"]);
                    break;
                case str_ends_with($checkEmail, '@collaborator.fr'):
                    $user->setRoles(["ROLE_COLLABORATOR"]);
                    break;
                case str_ends_with($checkEmail, '@external.fr'):
                    $user->setRoles(["ROLE_EXTERNAL"]);
                    break;
                default: 
                $this->addFlash('error', 'Votre adresse mail ne respecte pas les règles de validation'); 
                return new RedirectResponse($this->generateUrl('app_register'));
            }

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
