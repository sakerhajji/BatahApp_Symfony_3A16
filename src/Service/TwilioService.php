<?php

namespace App\Service;

use Twilio\Rest\Client;

class TwilioService
{
    private $sid;
    private $token;
    private $fromNumber;

    public function __construct(string $sid, string $token, string $fromNumber)
    {
        $this->sid = $sid;
        $this->token = $token;
        $this->fromNumber = $fromNumber;
    }

    public function sendSms(string $to, string $message): void
    {
        $client = new Client($this->sid, $this->token);

        $client->messages->create(
            $to,
            [
                'from' => $this->fromNumber,
                'body' => $message
            ]
        );
    }
}
