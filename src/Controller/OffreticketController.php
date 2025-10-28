<?php

namespace App\Controller;

use App\Entity\Offreticket;
use App\Form\OffreticketType;
use App\Repository\OffreticketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\Criteria;

#[Route('/offreticket')]
final class OffreticketController extends AbstractController
{
    #[Route('/{page<\d+>?1}', name: 'app_offreticket_index', methods: ['GET'])]
    public function index(OffreticketRepository $offreticketRepository, int $page = 1): Response
    {
        if($page < 1) $page = 1;

        $offreticketPerPage = 40;
        
        $criteria = Criteria::create()
            ->setFirstResult(($page - 1) * $offreticketPerPage)
            ->setMaxResults($offreticketPerPage);

        $offreticket = $offreticketRepository->matching($criteria);

        $totalOffreticketTicket = count($offreticketRepository->matching(Criteria::create()));

        $totalPages = ceil($totalOffreticketTicket / $offreticketPerPage);

        return $this->render('offreticket/index.html.twig', [
            'offretickets' => $offreticket,
            'currentPage'  => $page,
            'totalPages'   => $totalPages
        ]);
    }

    #[Route('/new', name: 'app_offreticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offreticket = new Offreticket();
        $form = $this->createForm(OffreticketType::class, $offreticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($offreticket);
            $entityManager->flush();

            return $this->redirectToRoute('app_offreticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offreticket/new.html.twig', [
            'offreticket' => $offreticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offreticket_show', methods: ['GET'])]
    public function show(Offreticket $offreticket): Response
    {
        return $this->render('offreticket/show.html.twig', [
            'offreticket' => $offreticket,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offreticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offreticket $offreticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreticketType::class, $offreticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offreticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offreticket/edit.html.twig', [
            'offreticket' => $offreticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offreticket_delete', methods: ['POST'])]
    public function delete(Request $request, Offreticket $offreticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offreticket->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offreticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offreticket_index', [], Response::HTTP_SEE_OTHER);
    }
}
