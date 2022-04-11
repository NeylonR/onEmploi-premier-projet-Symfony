<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\OfferRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
}
