<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\Criteria;

use App\Service\Mailer;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

#[Route('/ticket')]
final class TicketController extends AbstractController
{
    #[Route('/{page<\d+>?1}', name: 'app_ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository, int $page = 1): Response
    {
        if($page < 1) $page = 1;

        $ticketPerPage = 40;
        
        $criteria = Criteria::create()
            ->setFirstResult(($page - 1) * $ticketPerPage)
            ->setMaxResults($ticketPerPage);

        $ticket = $ticketRepository->matching($criteria);

        $totalTicket = count($ticketRepository->matching(Criteria::create()));

        $totalPages = ceil($totalTicket / $ticketPerPage);

        return $this->render('ticket/index.html.twig', [
            'tickets'      => $ticket,
            'currentPage'  => $page,
            'totalPages'   => $totalPages
        ]);
    }
/*
    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Mailer $mailer): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(Ticket1Type::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $this->getUser();

            if ($currentUser != null) {
                $ticket->setClefclient($currentUser->getclefCompte());

                $ticket->setClefticket($this->genererCleAleatoireTicket(20));

                $mailer->test($mailer);

                $QRcodePath = $this->GenerateQRcode($ticket->getClefclient() . $ticket->getClefticket());
                
                $this->SendQRcode($mailer, $QRcodePath);

                $entityManager->persist($ticket);
                $entityManager->flush();

                return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }
*/

    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

/*
    #[Route('/{id}/edit', name: 'app_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Ticket1Type::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }
*/
    #[Route('/{id}', name: 'app_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
    }

    function genererCleAleatoireTicket(int $longueur = 20) : string
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+';
        $cle = '';
        $max = strlen($caracteres) - 1;

        for ($i = 0; $i < $longueur; $i++) {
            $cle .= $caracteres[random_int(0, $max)];
        }

        return $cle;
    }

    function GenerateQRcode(string $code)
    {
        $writer = new PngWriter();

        $qrCode = new QrCode(
            data: $code,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin, 
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $result = $writer->write($qrCode);
        $path = $this->getParameter('kernel.project_dir') . '/public/qr_codes/';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $filename = 'qrcode_' . uniqid() . '.png';
        $filepath = $path . $filename;

        $result->saveToFile($filepath);

        return $filepath;
    }

    public function SendQRcode(Mailer $mailer, array $QRcodePath)
    {
        $currentUser = $this->getUser();
        $userEmail = $currentUser->getUserIdentifier();

        $mailer->EnvoiQRcode( $QRcodePath, $userEmail );
    }
}