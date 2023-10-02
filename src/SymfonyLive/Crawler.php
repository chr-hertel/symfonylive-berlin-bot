<?php

declare(strict_types=1);

namespace App\SymfonyLive;

use App\Entity\Slot;
use App\SymfonyLive\Crawler\Client;
use App\SymfonyLive\Crawler\Parser;

final readonly class Crawler
{
    public function __construct(
        private Client $client,
        private Parser $parser,
    ) {
    }

    /**
     * @return list<Slot>
     */
    public function loadSchedule(): array
    {
        return $this->parser->extractSlots(
            $this->client->getSchedule()
        );
    }
}
