<?php

// src/Service/TwilioService.php

namespace App\Service;

use Twilio\Http\CurlClient;
use Twilio\Rest\Client;

class twilioo
{
    private $sid;
    private $token;
    private $fromNumber;
    private $twilio;

    public function __construct(string $sid, string $token, string $fromNumber)
    {
        $this->sid = $sid;
        $this->token = $token;
        $this->fromNumber = $fromNumber;

        // Initialize Twilio client with default settings
        $this->twilio = new Client($this->sid, $this->token);
    }

    public function sendSms(string $to, string $message): void
    {
        // Create a new CurlClient instance with SSL verification disabled
        $httpClient = new CurlClient([CURLOPT_SSL_VERIFYPEER => false]);

        // Set the custom CurlClient instance
        $this->twilio->setHttpClient($httpClient);

        // Send SMS using Twilio client
        $this->twilio->messages->create(
            $to,
            [
                'from' => $this->fromNumber,
                'body' => $message
            ]
        );
    }
}
