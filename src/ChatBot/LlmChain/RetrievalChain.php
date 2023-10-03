<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain;

use App\ChatBot\LlmChain\Message\Message;
use App\ChatBot\LlmChain\Message\MessageBag;

final readonly class RetrievalChain implements LlmChainInterface
{
    public function __construct(
        private RetrieverInterface $retriever,
        private LlmChainInterface $chain,
    ) {
    }

    public function call(Message $message, MessageBag $messages): string
    {
        $retrievalPrompt = $this->retriever->enrich($message);

        return $this->chain->call($retrievalPrompt, $messages);
    }
}
