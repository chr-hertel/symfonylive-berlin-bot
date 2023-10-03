<?php

declare(strict_types=1);

namespace App\Tests\SymfonyLive\Crawler;

use App\Entity\Talk;
use App\SymfonyLive\Crawler\Parser;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    public function testSlotCollectionHasTheCorrectAmountOfSlots(): void
    {
        $parser = new Parser();
        $response = (string) file_get_contents(dirname(__DIR__).'/fixtures/full-schedule.html');

        $slots = $parser->extractSlots($response);

        self::assertCount(26, $slots);
    }

    public function testExtractingDescription(): void
    {
        $parser = new Parser();
        $response = (string) file_get_contents(dirname(__DIR__).'/fixtures/full-schedule.html');

        $expectedDescription = 'SQL-Datenbanken sind integraler Bestandteil vieler Webanwendungen. Doch wie interagiere ich mit der Datenbank am Besten? Schreibe ich alle SQL-Statements per Hand, um maximale Kontrolle zu behalten? Oder verwende ich ein ORM, um mich weniger zu wiederholen und zu mehreren SQL-Dialekten kompatibel zu bleiben? Wir werfen einen Blick auf die Doctrine-Bibliotheken ORM und DBAL sowie native PHP-Erweiterungen und vergleichen sie für unsere Anwendungsfälle.';
        $actualDescription = '';

        $slots = $parser->extractSlots($response);
        foreach ($slots as $slot) {
            foreach ($slot->getEvents() as $event) {
                if ('Wie viel Datenbankabstraktion brauche ich?' === $event->getTitle()) {
                    self::assertInstanceOf(Talk::class, $event);
                    $actualDescription = $event->getDescription();
                }
            }
        }

        self::assertSame($expectedDescription, $actualDescription);
    }
}
