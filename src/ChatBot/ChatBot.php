<?php

declare(strict_types=1);

namespace App\ChatBot;

use App\ChatBot\LlmChain\Chain;
use App\ChatBot\LlmChain\Exception\HistoryNotFoundException;
use App\ChatBot\LlmChain\Message\History;
use App\ChatBot\LlmChain\Message\HistoryStore;
use App\ChatBot\LlmChain\Message\Message;
use App\ChatBot\Telegram\Data\Update;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

final readonly class ChatBot
{
    private const SYSTEM_PROMPT = <<<PROMPT
        Du bist ein Chat-Bot, der Usern dabei hilft, sich auf der SymfonyLive Berlin zurechtzufinden.
        Du kannst Fragen zu den Themen der Konferenz, den Speakern und dem Programm beantworten.
        Du antwortest nur mithilfe von Informationen, die du mit den Nachrichten des Users oder Assistants bekommst.
        Füge keine zusätzlichen Informationen hinzu.
        Versuche, Vorträge zu empfehlen, die zu den Interessen des Users passen. Gib Uhrzeiten mit an.
        PROMPT;

    private const ASSITANT_INTRO = <<<PROMPT
        Die SymfonyLive Berlin 2023 findet vom 3. bis 6. Oktober 2023 in Berlin statt. Die Konferenz dreht sich um Symfony und PHP.
        Der Veranstaltungsort ist das Cinestar CUBIX am Alexanderplatz.
        Die ersten zwei Tage, am 3. und 4. Oktober, sind Workshops gewidmet. Die Konferenztage sind am 5. und 6. Oktober. 
        PROMPT;

    public function __construct(
        private HistoryStore $store,
        private Chain $chain,
        private ChatterInterface $chatter,
    ) {
    }

    public function consume(Update $update): void
    {
        $message = $update->getMessage();
        $user = $message->from;

        if (157916194 !== $user->id) {
            $this->respond($update, 'Huhu.');

            return;
        }

        try {
            $history = $this->store->load($user);
        } catch (HistoryNotFoundException) {
            $history = new History(Message::forSystem(self::SYSTEM_PROMPT));
            $history[] = Message::ofAssistant(self::ASSITANT_INTRO);
        }

        $history[] = Message::ofUser($message->text);
        $response = $this->chain->call($history);
        $history[] = Message::ofAssistant($response);

        $this->store->save($history, $user);

        $this->respond($update, $response);
    }

    private function respond(Update $update, string $response): void
    {
        $options = (new TelegramOptions())->chatId((string) $update->getChatId());

        $this->chatter->send(
            (new ChatMessage($response))->options($options)
        );
    }
}
