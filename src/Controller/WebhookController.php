<?php

declare(strict_types=1);

namespace App\Controller;

use App\ChatBot\ChatBot;
use App\ChatBot\Telegram\Data\Update;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[AsController]
final readonly class WebhookController
{
    public function __construct(private ChatBot $chatBot)
    {
    }

    #[Route('/chatbot', name: 'webhook', methods: ['POST'], defaults: ['_format' => 'json'])]
    public function connect(
        #[MapRequestPayload(serializationContext: [DateTimeNormalizer::FORMAT_KEY => 'U'])] Update $update
    ): Response {
        $this->chatBot->consume($update);

        return new Response();
    }
}
