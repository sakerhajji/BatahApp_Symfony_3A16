<?php

namespace App\Service;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\Dsn;

class EmailSender
{
    public function sendEmail()
    {
        // Create a Transport object
        $transport = Transport::fromDsn('gmail://hamzosayari07@gmail.com:211JMT5224@smtp.gmail.com:587');

        // Create a Mailer object
        $mailer = new Mailer($transport);

        // Create an Email object
        $email = (new Email())
            ->from('hamzosayari07@gmail.com')
            ->to('hamzos9mm@gmail.com')
            ->subject('A Cool Subject!')
            ->text('The plain text version of the message.');

        // Send the email
        $mailer->send($email);
    }
}
