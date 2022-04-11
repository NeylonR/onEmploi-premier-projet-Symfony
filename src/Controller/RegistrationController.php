<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Candidate;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RegistrationCandidateFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registerCompany', name: 'app_register_company')]
    public function registerCompany(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_home');
        }
        $user = new Company();
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
            $user->setCity($form->get('city')->getData())
                 ->setName($form->get('name')->getData())
                 ->setEmail($form->get('email')->getData());

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/registerCandidate', name: 'app_register_candidate')]
    public function registerCandidate(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_home');
        }

        $user = new Candidate();
        $form = $this->createForm(RegistrationCandidateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user, 
                    $form->get('plainPassword')->getData()
                    )
                );
            $user->setCity($form->get('city')->getData())
                 ->setFirstName($form->get('firstName')->getData())
                 ->setLastName($form->get('lastName')->getData())
                 ->setEmail($form->get('email')->getData());

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/registerCandidate.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
