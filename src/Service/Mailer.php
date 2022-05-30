<?php
namespace App\Service;

class Mailer
{
    private $adminEmail;

    public function __construct(string $adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    public function sendMail($adminEmail): bool
    {
        if($adminEmail === true) {
            return 'Votre message à bien été envoyé';
        } else {
            return 'Une erreur s\est produite, veuillez rééssayer.';
        }
    }

}