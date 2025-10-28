<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Exception;

class Mailer
{
    private $mailer;
    private string $applicationAdresse = "kentguillou@hotmail.fr";

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function ErrorSendMail($error): void
    {
        echo('Une erreur est apparue: ' . $error . '\n' . 'Impossible d\'envoyer le mail');
    }

    public function EnvoiQRcode( array $arrayQRcode, string $emailSender ): bool
    {
        $i = 1;

        try{
            $email = (new Email())
            ->from($this->applicationAdresse)
            ->to($emailSender)
            ->subject("Vos tickets JO 2024")
            ->html(
                "<h1>Vos tickets JO 2024</h1>" 
            );

            foreach ($arrayQRcode as $path) {
                $email->attach(fopen($path, 'r'), "QRcode$i.png");
                $i++;
            }

            $this->mailer->send($email);

            return true;

        } catch(Exception $e) {
            $this->ErrorSendMail($e);
            
            return false;
        }
    }
}