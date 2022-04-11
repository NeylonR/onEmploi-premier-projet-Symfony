<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Company;
use App\Entity\Candidate;
use App\Form\OfferFormType;
use App\Repository\CandidateRepository;
use Doctrine\ORM\Mapping\OrderBy;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Query\Expr\OrderBy as ExprOrderBy;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/offer')]
class OfferController extends AbstractController
{
    #[Route('/', name: 'app_offer_index')]
    public function index(OfferRepository $offerRepo): Response
    {
        $offerRepo = $offerRepo->findBy(array(), array('updatedAt'=>'DESC'));

        return $this->render('offer/index.html.twig', [
            'offers' => $offerRepo,
        ]);
    }

    #[Route('/create', name: 'app_offer_create')]
    #[IsGranted('ROLE_COMPANY', message: 'You must be logged-in to access this resource')]
    public function newForm(Request $request, EntityManagerInterface $manager): Response
    {
        if($this->getUser()){
            $offer = new Offer();
            $form = $this->createForm(OfferFormType::class, $offer);
            $user = $this->getUser();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $offer->setCreatedAt(new \Datetime('now'))
                ->setAuthor($user);
                $manager->persist($offer);
                $manager->flush();

                return $this->redirectToRoute('app_offer_index');
            }
            return $this->render('offer/createOffer.html.twig', [
                'form' => $form->createView()
            ]);
        }

        return $this->redirectToRoute('app_offer_index');
    }

    #[Route('/id={id}', name: 'app_offer_details', requirements:['id' => '\d+'])]
    public function details(Offer $offer, CandidateRepository $candidateRepository): Response
    {
        // $offerRepo = $offerRepo->find($id);

        $user = $this->getUser();
        $candidatesAvailable = [];

        if ($offer->getAuthor() === $user) {
            $candidates = $candidateRepository->findAll();
            foreach ($candidates as $candidate) {
                foreach($candidate->getContractType() as $candidateContractType){
                    if ($candidateContractType === $offer->getContractType()) {
                        $candidatesAvailable[] = $candidate;
                    }
                }
            }
        }
        
        if($offer){
            return $this->render('offer/details.html.twig', [
                'offer' => $offer,
                'candidates' => $candidatesAvailable
            ]);
        }
        return $this->redirectToRoute('app_offer_index');
    }

    #[Route('/edit/id={id}', name: 'app_offer_edit', requirements:['id' => '\d+'])]
    #[IsGranted('ROLE_COMPANY', message: 'You must be logged-in to access this resource')]
    public function edit(OfferRepository $offerRepo, $id, Request $request, EntityManagerInterface $manager, Offer $offer): Response
    {

        if($this->getUser() === $offer->getAuthor()){
            $form = $this->createForm(OfferFormType::class, $offer);
            $user = $this->getUser();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $offer->setUpdatedAt(new \Datetime('now'))
                ->setAuthor($user);

                $manager->flush();

                return $this->redirectToRoute('app_offer_index');
            }

            return $this->render('offer/createOffer.html.twig', [
                'form' => $form->createView(),
                'offer' => $offer
            ]);
        }

        return $this->redirectToRoute('app_offer_index');
        
    }

    #[Route('/delete/id={id}', name: 'app_offer_delete', requirements:['id' => '\d+'])]
    #[IsGranted('ROLE_COMPANY', message: 'You must be logged-in to access this resource')]
    public function delete(OfferRepository $offerRepo, $id, Request $request, EntityManagerInterface $manager, Offer $offer): Response
    {
        $offerRepo = $offerRepo->find($id);

        if ($this->getUser() === $offer->getAuthor() && $this->isCsrfTokenValid('offer_delete_'.$offer->getId(), $request->request->get('csrf_token')) || $this->isGranted('ROLE_ADMIN')) {
            $manager->remove($offer);
            $manager->flush();
        }

        return $this->redirectToRoute('app_offer_index');
    }

    #[Route('/apply/{id<\d+>}', name: 'app_offer_apply')]
    #[IsGranted('ROLE_CANDIDATE', message: 'You must be logged-in to access this resource')]
    public function apply(EntityManagerInterface $manager, Offer $offer): Response
    {
        $user = $this->getUser();

        if($offer->getApplicants()->contains($user)){
            $offer->removeApplicant($user);
            $manager->flush();
        }else{
            $offer->addApplicant($user);
            $manager->flush();
        }

        return $this->redirectToRoute('app_candidate_offers');
    }
}
