<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain\Message;

enum Role: string
{
    case System = 'system';
    case Assistant = 'assistant';
    case User = 'user';
}
