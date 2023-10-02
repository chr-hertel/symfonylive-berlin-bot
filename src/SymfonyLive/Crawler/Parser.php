<?php

declare(strict_types=1);

namespace App\SymfonyLive\Crawler;

use App\Entity\Event;
use App\Entity\Slot;
use App\Entity\Talk;
use App\Entity\TimeSpan;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DomCrawler\Crawler;

final class Parser
{
    public function __construct(private readonly LoggerInterface $logger = new NullLogger())
    {
    }

    /**
     * @return list<Slot>
     */
    public function extractSlots(string $response): array
    {
        $crawler = new Crawler($response);
        $slots = [];
        /** @var ?Slot $prevSlot */
        $prevSlot = null;

        // Extract slots
        $crawler->filter('.schedule-row')->each(function (Crawler $row) use ($crawler, &$slots, &$prevSlot) {
            $startsAt = $row->filter('.schedule-time')->attr('data-starts-at');
            $endsAt = $row->filter('.schedule-time')->attr('data-ends-at');

            if (null === $startsAt || null === $endsAt) {
                $this->logger->warning('Cannot collect start or end time for slot');

                return;
            }

            $start = (new \DateTimeImmutable($startsAt))->setTimezone(new \DateTimeZone('Europe/Berlin'));
            $end = (new \DateTimeImmutable($endsAt))->setTimezone(new \DateTimeZone('Europe/Berlin'));

            try {
                $timeSpan = new TimeSpan($start, $end);
            } catch (\InvalidArgumentException $e) {
                $this->logger->warning('Cannot create TimeSpan for slot', [
                    'exception' => $e,
                    'startsAt' => $startsAt,
                    'endsAt' => $endsAt,
                ]);

                return;
            }
            $slot = new Slot($timeSpan, $prevSlot);
            $prevSlot?->setNext($slot);

            // Extract events
            $row->filter('.schedule-event')->each(function (Crawler $event) use ($slot, $timeSpan) {
                $title = $event->filter('.schedule-event-title')->text();

                $slot->addEvent(new Event($title, $timeSpan, $slot));
            });

            // Extract talks
            $row->filter('.schedule-talk')->each(function (Crawler $talk, int $index) use ($crawler, $slot, $timeSpan) {
                $title = $talk->filter('.schedule-talk-title')->text();
                $id = $talk->filter('.schedule-talk-title')->attr('href');

                $slot->addEvent(new Talk(
                    $title,
                    $talk->filter('.schedule-talk-author')->text(),
                    null !== $id ? $crawler->filter('.schedule-list '.$id.' .editable-content')->text() : '',
                    $timeSpan,
                    $slot,
                ));
            });

            $slots[] = $slot;
            $prevSlot = $slot;
        });

        return $slots;
    }
}
