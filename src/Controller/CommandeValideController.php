<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\OffreticketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


use App\Service\Mailer;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;

final class CommandeValideController extends AbstractController
{
    #[Route('/commandevalide', name: 'app_commandevalide', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('commandeValide/index.html.twig', [
        ]);
    }

    #[Route('/commandevalideupdate', name: 'app_commandevalide_update', methods: ['GET'])]
    public function RequeteDQLUpdateCount(
        OffreticketRepository $offreticketRepository, 
        EntityManagerInterface $entityManager,
        Mailer $mailer
    )
    {
        $currentUser = $this->getUser();

        $arrayQRcode = [];

        if ($currentUser != null) {
            foreach($_GET as $key => $value) {
                if($value > 0) {
                    $newKey = str_replace('nombreDePlace', '', $key);
                    $numberPlace = $offreticketRepository->valideAchat((int)$newKey, $value); 

                    for($i = 0; $i < $value; $i++) {
                        array_push($arrayQRcode, $this->CreateTicket($entityManager, $numberPlace));
                    } 
                }
            };

            $userEmail = $currentUser->getUserIdentifier();

            $mailer->EnvoiQRcode( $arrayQRcode, $userEmail );

            $this->DeleteQRcode($arrayQRcode);
            
            $response = new Response();
            $response->setContent(json_encode("sucess"));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    public function CreateTicket(
        EntityManagerInterface $entityManager, 
        $numberPlace
    ) : string
    {
        $currentUser = $this->getUser();

        $userKey     = $currentUser->getclefCompte();
        $generateKey = $this->genererCleAleatoireTicket(20);
        
        $ticket = new Ticket();
        $ticket->setClefclient($userKey);
        $ticket->setClefticket($generateKey);
        $ticket->setNombreDePlace($numberPlace[0]["nombrePlace"]);

        $entityManager->persist($ticket);
        $entityManager->flush();

        $fullCode = $userKey . $generateKey;

        return $this->GenerateQRcode((string)$fullCode);
    }

    function GenerateQRcode(string $code) : string
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

    function DeleteQRcode(array $arrayQRcode) {

        foreach($arrayQRcode as $key => $value) {
            unlink($value);
        }
    }
}