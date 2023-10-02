<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain\Message;

final readonly class Message
{
    public function __construct(
        public string $content,
        public Role $role,
    ) {
    }

    public static function forSystem(string $content): self
    {
        return new self($content, Role::System);
    }

    public static function ofAssistant(string $content): self
    {
        return new self($content, Role::Assistant);
    }

    public static function ofUser(string $content): self
    {
        return new self($content, Role::User);
    }
}
