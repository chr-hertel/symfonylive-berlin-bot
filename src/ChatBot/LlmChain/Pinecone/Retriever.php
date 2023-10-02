<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain\Pinecone;

use App\ChatBot\LlmChain\Message\History;
use App\ChatBot\LlmChain\Message\Message;
use App\ChatBot\LlmChain\OpenAI\Embeddings;
use App\Repository\EventRepository;

final readonly class Retriever
{
    public function __construct(
        private Embeddings $embeddings,
        private Client $client,
        private EventRepository $eventRepository,
    ) {
    }

    public function enrich(History $history): void
    {
        $latestMessage = $history->offsetGet($history->count() - 1);
        $vector = $this->embeddings->create($latestMessage->content);
        $ids = $this->client->query($vector);

        $found = 'Folgende Vorträge oder Events habe ich dazu gefunden: ';
        foreach ($ids as $id) {
            $event = $this->eventRepository->find($id); // single query due to some sqlite thingy

            if (null === $event) {
                continue;
            }

            $found .= $event->toString().PHP_EOL.PHP_EOL;
        }

        $history[] = Message::ofAssistant($found);
    }
}
