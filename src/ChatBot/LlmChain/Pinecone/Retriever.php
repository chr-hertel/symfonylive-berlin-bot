<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain\Pinecone;

use App\ChatBot\LlmChain\Message\MessageBag;
use App\ChatBot\LlmChain\Message\Message;
use App\ChatBot\LlmChain\OpenAI\Embeddings;
use App\ChatBot\LlmChain\RetrieverInterface;
use App\Repository\EventRepository;

final readonly class Retriever implements RetrieverInterface
{
    public function __construct(
        private Embeddings $embeddings,
        private Client $client,
        private EventRepository $eventRepository,
    ) {
    }

    public function enrich(Message $message): Message
    {
        $vector = $this->embeddings->create($message->content);
        $ids = $this->client->query($vector);

        $prompt = <<<PROMPT
            Beantworte mithilfe folgender Informationen oder Informationen aus vorherigen Nachrichten die Frage ganz am Ende.
            Füge dabei keine Informationen hinzu und wenn du keine Antwort findest, sag es.
            PROMPT;

        foreach ($ids as $id) {
            $event = $this->eventRepository->find($id); // single query due to some sqlite thingy

            if (null === $event) {
                continue;
            }

            $prompt .= $event->toString().PHP_EOL;
        }

        $prompt .= '. Frage: '.$message->content;

        return Message::ofUser($prompt);
    }
}
