<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        if ($this->isGranted('ROLE_CANDIDATE')) {
            return $this->redirectToRoute('app_candidate_profile');
        }
        if ($this->isGranted('ROLE_COMPANY')) {
            return $this->redirectToRoute('app_company_profile');
        }
        return $this->redirectToRoute('app_offer_index');
    }
}
