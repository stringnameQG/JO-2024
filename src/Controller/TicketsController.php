<?php

namespace App\Controller;

use App\Repository\OffreticketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TicketsController extends AbstractController
{
    #[Route('/tickets', name: 'app_tickets', methods: ['GET'])]
    public function index(OffreticketRepository $offreticketRepository): Response
    {
        return $this->render('tickets/index.html.twig', [
            'controller_name' => 'TicketsController',
            'offretickets' => $offreticketRepository->findAll()
        ]);
    }
}