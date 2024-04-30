<?php

namespace App\Controller;

use OpenAI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BatahGptController extends AbstractController
{
        #[Route('/chatgpt', name: 'app_chatgpt')]
    public function index( ? string $question, ? string $response): Response
    {
        return $this->render('batah_gpt/index.html.twig', [
            'question' => $question,
            'response' => $response
        ]);
    }
    #[Route('/chat', name: 'send_chat', methods:"POST")]
    public function chat(Request $request): Response
    {
        $question=$request->request->get('text');


        $myApiKey = $_ENV['OPENAI_KEY'];


        $client = OpenAI::client($myApiKey);

        $result = $client->completions()->create([
            'model' => 'babbage-002',
            'prompt' => $question,
            'max_tokens'=>2048
        ]);

        $response=$result->choices[0]->text;


        return $this->forward('App\Controller\batahGptController::index', [

            'question' => $question,
            'response' => $response
        ]);
    }
}
