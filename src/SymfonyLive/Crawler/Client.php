<?php

declare(strict_types=1);

namespace App\SymfonyLive\Crawler;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Client
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function getSchedule(): string
    {
        $response = $this->httpClient->request('GET', 'https://live.symfony.com/2023-berlin/schedule');

        return $response->getContent();
    }
}
