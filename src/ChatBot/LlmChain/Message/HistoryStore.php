<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain\Message;

use App\ChatBot\LlmChain\Exception\HistoryNotFoundException;
use App\ChatBot\Telegram\Data\User;
use Psr\Cache\CacheItemPoolInterface;

final readonly class HistoryStore
{
    public function __construct(private CacheItemPoolInterface $cache)
    {
    }

    public function load(User $user): History
    {
        $item = $this->cache->getItem('history_'.$user->id);

        if (!$item->isHit()) {
            throw new HistoryNotFoundException();
        }

        return $item->get();
    }

    public function save(History $history, User $user): void
    {
        $item = $this->cache->getItem('history_'.$user->id);
        $item->set($history);
        $this->cache->save($item);
    }
}
