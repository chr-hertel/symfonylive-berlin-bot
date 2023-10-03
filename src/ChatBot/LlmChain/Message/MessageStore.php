<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain\Message;

use App\ChatBot\LlmChain\Exception\MessageBagNotFoundException;
use App\ChatBot\Telegram\Data\User;
use Psr\Cache\CacheItemPoolInterface;

final readonly class MessageStore
{
    public function __construct(private CacheItemPoolInterface $cache)
    {
    }

    public function load(User $user): MessageBag
    {
        $item = $this->cache->getItem('messages_'.$user->id);

        if (!$item->isHit()) {
            throw new MessageBagNotFoundException();
        }

        return $item->get();
    }

    public function save(MessageBag $messages, User $user): void
    {
        $item = $this->cache->getItem('messages_'.$user->id);
        $item->set($messages);
        $this->cache->save($item);
    }
}
