<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain\OpenAI;

final readonly class Embeddings
{
    public function __construct(
        private Client $client,
        private string $model = 'text-embedding-ada-002',
    ) {
    }

    public function create(string $text): array
    {
        $body = [
            'model' => $this->model,
            'input' => $text,
        ];

        $response = $this->client->request('embeddings', $body);

        return $response['data'][0]['embedding'];
    }
}
