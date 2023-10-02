<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TalkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TalkRepository::class)]
class Talk extends Event
{
    public function __construct(
        string $title,
        #[ORM\Column(nullable: true)]
        private readonly string $speaker,
        #[ORM\Column(type: 'text')]
        private readonly string $description,
        TimeSpan $timeSpan,
        Slot $slot,
    ) {
        parent::__construct($title, $timeSpan, $slot);
    }

    public function getTitle(): string
    {
        return parent::getTitle();
    }

    public function getSpeaker(): string
    {
        return $this->speaker;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isOver(\DateTimeImmutable $now): bool
    {
        return $now > $this->getTimeSpan()->getEnd();
    }

    public function toString(): string
    {
        return sprintf('Uhrzeit: %s Vortrag: %s. Speaker: %s. Beschreibung: %s', $this->getTimeSpan()->toString(), $this->getTitle(), $this->getSpeaker(), $this->getDescription());
    }
}
