<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class TimeSpan
{
    public function __construct(
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $start,
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $end,
    ) {
        $this->start = $this->enforceBerlinTimeZone($this->start);
        $this->end = $this->enforceBerlinTimeZone($this->end);

        if ($this->start > $this->end) {
            throw new \InvalidArgumentException('The time span needs to start before it ends.');
        }
    }

    public function getStart(): \DateTimeImmutable
    {
        return $this->enforceBerlinTimeZone($this->start);
    }

    public function getEnd(): \DateTimeImmutable
    {
        return $this->enforceBerlinTimeZone($this->end);
    }

    public function toString(): string
    {
        return sprintf('%s - %s', $this->getStart()->format('M d Y: H:i'), $this->getEnd()->format('H:i'));
    }

    private function enforceBerlinTimeZone(\DateTimeImmutable $dateTime): \DateTimeImmutable
    {
        return $dateTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
    }
}
