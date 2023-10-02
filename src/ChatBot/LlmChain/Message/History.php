<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain\Message;

final class History extends \ArrayObject implements \JsonSerializable
{
    public function __construct(Message ...$messages)
    {
        parent::__construct($messages);
    }

    /**
     * @return array<array{role: 'system'|'assistant'|'user', content: string}>
     */
    public function jsonSerialize(): array
    {
        return array_map(
            fn (Message $message) => ['role' => $message->role->value, 'content' => $message->content],
            $this->getArrayCopy(),
        );
    }
}
