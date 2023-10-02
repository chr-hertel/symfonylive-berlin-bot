<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain\OpenAI;

use App\ChatBot\LlmChain\Message\History;

final readonly class ChatModel
{
    public function __construct(
        private Client $client,
        private string $model = 'gpt-4',
        private float $temperature = 1.0,
    ) {
    }

    public function call(History $history): array
    {
        $body = [
            'model' => $this->model,
            'messages' => $history,
            'temperature' => $this->temperature,
        ];

        return $this->client->request('chat/completions', $body);
    }
}
