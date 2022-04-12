<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Form\PhotoProfileType;
use App\Form\ChangePasswordType;
use App\Repository\OfferRepository;
use App\Form\PhotoProfileCompanyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/company')]
#[IsGranted('ROLE_COMPANY', message: 'You must be logged-in to access this resource')]
class CompanyController extends AbstractController
{
    #[Route('/offers', name: 'app_company_offers')]
    public function offers(OfferRepository $offerRepository): Response
    {
        $offers = $offerRepository->findBy(['author' => $this->getUser()->getUserId()], ['createdAt' => 'DESC']);

        return $this->render('company/offers.html.twig', [
            'offers' => $offers,
        ]);
    }

    #[Route('/profile', name: 'app_company_profile')]
    public function profile(): Response
    {

        $user = $this->getUser();
        if($user){
            return $this->render('company/index.html.twig', [
                'company' => $user,
                'action' => false
            ]);
        }
        return $this->redirectToRoute('app_home');
    }

    #[Route('/profile/editPhoto', name: 'app_company_editPhoto')]
    public function editPhoto(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PhotoProfileCompanyType::class, $user);

        $form->handleRequest($request);
        
            if($form->isSubmitted() && $form->isValid()){
                $manager->flush();
                $this->getUser()->setLogoFile(null);

                return $this->redirectToRoute('app_company_profile');
            } 
           
        
        return $this->render('company/index.html.twig', [
            'company' => $user,
            'action' => 'modifyPhoto',
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/edit', name: 'app_company_edit')]
    public function edit(Request $request, EntityManagerInterface $manager): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(CompanyType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->flush();

            return $this->redirectToRoute('app_company_profile');
        }
        return $this->render('company/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/editPassword', name: 'app_company_editPassword')]
    public function editPassword(UserPasswordHasherInterface $userPasswordHasher, Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);
        
            if($form->isSubmitted() && $form->isValid()){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user, 
                        $form->get('plainPassword')->getData()
                        )
                    );
                $manager->flush();

                return $this->redirectToRoute('app_company_profile');
            } 
           
        
        return $this->render('company/index.html.twig', [
            'company' => $user,
            'action' => 'modifyPassword',
            'form' => $form->createView()
        ]);
    }
}
