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
        $lastIndex = $history->count() - 1;
        $latestMessage = $history->offsetGet($lastIndex);
        $history->offsetUnset($lastIndex);
        $vector = $this->embeddings->create($latestMessage->content);
        $ids = $this->client->query($vector);

        $found = <<<PROMPT
            Beantworte mithilfe folgender Informationen die Frage ganz am Ende.
            Füge dabei keine Informationen hinzu und wenn du keine Antwort findest, sag es.
            PROMPT;

        foreach ($ids as $id) {
            $event = $this->eventRepository->find($id); // single query due to some sqlite thingy

            if (null === $event) {
                continue;
            }

            $found .= $event->toString().PHP_EOL;
        }

        $found .= '. Frage: '.$latestMessage->content;

        $history->offsetSet($lastIndex, Message::ofUser($found));
    }
}
