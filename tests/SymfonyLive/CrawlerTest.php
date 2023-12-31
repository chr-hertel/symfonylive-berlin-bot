<?php

declare(strict_types=1);

namespace App\Tests\SymfonyLive;

use App\SymfonyLive\Crawler;
use App\SymfonyLive\Crawler\Client;
use App\SymfonyLive\Crawler\Parser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class CrawlerTest extends TestCase
{
    public function testFetchData(): void
    {
        $httpClient = new MockHttpClient($this->responseFactory(...));
        $client = new Client($httpClient);
        $crawler = new Crawler($client, new Parser());

        $slots = $crawler->loadSchedule();

        self::assertCount(26, $slots);
    }

    private function responseFactory(string $method, string $url): ResponseInterface
    {
        self::assertSame('GET', $method);
        self::assertSame('https://live.symfony.com/2023-berlin/schedule', $url);

        return new MockResponse((string) file_get_contents(__DIR__.'/fixtures/full-schedule.html'));
    }
}
