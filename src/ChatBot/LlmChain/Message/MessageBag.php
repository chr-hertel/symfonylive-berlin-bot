<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain\Message;

final class MessageBag extends \ArrayObject implements \JsonSerializable
{
    public function __construct(Message ...$messages)
    {
        parent::__construct($messages);
    }

    public function with(Message $message): self
    {
        $messages = clone $this;
        $messages->append($message);

        return $messages;
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
