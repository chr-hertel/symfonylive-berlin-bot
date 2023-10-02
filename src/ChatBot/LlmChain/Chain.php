<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain;

use App\ChatBot\LlmChain\Message\History;
use App\ChatBot\LlmChain\OpenAI\ChatModel;
use App\ChatBot\LlmChain\Pinecone\Retriever;
use Psr\Log\LoggerInterface;

final readonly class Chain
{
    public function __construct(
        private Retriever $retriever,
        private ChatModel $model,
        private LoggerInterface $logger,
    ) {
    }

    public function call(History $history): string
    {
        $this->logger->debug('Calling to chain with history', ['history' => $history]);

        $this->retriever->enrich($history);
        $response = $this->model->call($history);

        $this->logger->debug('Received response from model', ['response' => $response]);

        return $response['choices'][0]['message']['content'];
    }
}
