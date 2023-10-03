<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain;

use App\ChatBot\LlmChain\Message\MessageBag;
use App\ChatBot\LlmChain\Message\Message;

interface LlmChainInterface
{
    public function call(Message $message, MessageBag $messages): string;
}
