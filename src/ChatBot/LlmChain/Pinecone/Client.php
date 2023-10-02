<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain\Pinecone;

use Probots\Pinecone\Client as Pinecone;

final readonly class Client
{
    private const INDEX = 'symfonylive-berlin-bot';

    public function __construct(private Pinecone $pinecone)
    {
    }

    public function query(array $vector): array
    {
        $response = $this->pinecone->index(self::INDEX)
            ->vectors()
            ->query($vector);

        return array_map(fn (array $match) => $match['id'], $response->json()['matches']);
    }

    public function upsert(array $vectors): void
    {
        $this->pinecone->index(self::INDEX)
            ->vectors()
            ->upsert($vectors);
    }

    public function truncate(): void
    {
        $this->pinecone->index(self::INDEX)
            ->vectors()
            ->delete(deleteAll: true);
    }
}
