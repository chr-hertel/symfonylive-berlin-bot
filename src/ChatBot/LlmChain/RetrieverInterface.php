<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain;

use App\ChatBot\LlmChain\Message\Message;

interface RetrieverInterface
{
    public function enrich(Message $message): Message;
}
