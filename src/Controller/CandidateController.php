<?php

namespace App\Controller;

use App\Entity\Cv;
use App\Form\CvType;
use App\Entity\Candidate;
use App\Form\CandidateType;
use App\Form\PhotoProfileType;
use App\Form\ChangePasswordType;
use App\Repository\OfferRepository;
use App\Repository\CandidateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/candidate')]
#[IsGranted('ROLE_CANDIDATE', message: 'You must be logged-in to access this resource')]
class CandidateController extends AbstractController
{
    #[Route('/profile', name: 'app_candidate_profile')]
    public function profile(): Response
    {

        $user = $this->getUser();
        if($user){
            return $this->render('candidate/index.html.twig', [
                'candidate' => $user,
                'action' => false
            ]);
        }
        return $this->redirectToRoute('app_home');
    }

    #[Route('/profile/edit', name: 'app_candidate_edit')]
    public function edit(Request $request, EntityManagerInterface $manager): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(CandidateType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->flush();

            return $this->redirectToRoute('app_candidate_profile');
        }
        return $this->render('candidate/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/editPassword', name: 'app_candidate_editPassword')]
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

                return $this->redirectToRoute('app_candidate_profile');
            } 
           
        
        return $this->render('candidate/index.html.twig', [
            'candidate' => $user,
            'action' => 'modifyPassword',
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/editPhoto', name: 'app_candidate_editPhoto')]
    public function editPhoto(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PhotoProfileType::class, $user);

        $form->handleRequest($request);
        
            if($form->isSubmitted() && $form->isValid()){
                $manager->flush();
                $this->getUser()->setPhotoFile(null);

                return $this->redirectToRoute('app_candidate_profile');
            } 
           
        
        return $this->render('candidate/index.html.twig', [
            'candidate' => $user,
            'action' => 'modifyPhoto',
            'form' => $form->createView()
        ]);
    }

    #[Route('/offers', name: 'app_candidate_offers')]
    public function offers(CandidateRepository $candidateRepository, OfferRepository $offerRepository): Response
    {
        $user = $candidateRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]); // Returns a Candidate object

        $offers = $offerRepository->findAll();
        $offersFiltered = [];
        
        foreach ($offers as $offer) {
            foreach ($user->getContractType() as $userContractType) {
                if ($offer->getContractType() === $userContractType) {
                    $offersFiltered[] = $offer;
                }
            }
        }

        return $this->render('candidate/offers.html.twig', [
            'offers' => $offersFiltered,
        ]);
    }

    #[Route('/applications', name: 'app_candidate_applications')]
    public function applications(CandidateRepository $candidateRepository): Response
    {
        $user = $candidateRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]); // Returns a Candidate object

        $offers = $user->getApplications();

        return $this->render('candidate/applications.html.twig', [
            'offers' => $offers,
        ]);
    }
}
