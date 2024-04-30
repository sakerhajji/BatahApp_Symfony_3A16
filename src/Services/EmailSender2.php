<?php

namespace App\Services;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\Dsn;

class EmailSender2
{
    public function sendEmail(string $to , string $subject , string $html,string $imagePath)
    {
        // Create a Transport object
        $transport = Transport::fromDsn('smtp://batahapp@gmail.com:gpay%20ypxn%20mcnf%20uiod@smtp.gmail.com:587');

        // Create a Mailer object
        $mailer = new Mailer($transport);

        // Create an Email object
        $email = (new Email())
            ->from('batahapp@gmail.com')
            ->to($to)
            ->subject($subject)
            ->html($html);
        $email->embedFromPath($imagePath, 'logo');
        // Send the email
        $mailer->send($email);
    }

}