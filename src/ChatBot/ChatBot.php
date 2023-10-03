<?php

declare(strict_types=1);

namespace App\ChatBot;

use App\ChatBot\LlmChain\Exception\MessageBagNotFoundException;
use App\ChatBot\LlmChain\Message\Message;
use App\ChatBot\LlmChain\Message\MessageBag;
use App\ChatBot\LlmChain\Message\MessageStore;
use App\ChatBot\LlmChain\RetrievalChain;
use App\ChatBot\Telegram\Data\Update;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

final readonly class ChatBot
{
    private const SYSTEM_PROMPT = <<<PROMPT
        Du bist ein Chat-Bot, der Usern dabei hilft, sich auf der SymfonyLive Berlin zurechtzufinden.
        Du kannst Fragen zu den Themen der Konferenz, den Speakern und dem Programm beantworten.
        PROMPT;

    private const ASSISTANT_INTRO = <<<PROMPT
        Die SymfonyLive Berlin 2023 findet vom 3. bis 6. Oktober 2023 in Berlin statt. Die Konferenz dreht sich um Symfony und PHP.
        Der Veranstaltungsort ist das Cinestar CUBIX am Alexanderplatz.
        Die ersten zwei Tage, am 3. und 4. Oktober, sind Workshops gewidmet. Die Konferenztage sind am 5. und 6. Oktober. 
        PROMPT;

    public function __construct(
        private MessageStore     $store,
        private RetrievalChain   $chain,
        private ChatterInterface $chatter,
    ) {
    }

    public function consume(Update $update): void
    {
        $user = $update->getSender();
        $text = $update->getMessageText();
        $message = Message::ofUser($text);

        try {
            $messages = $this->store->load($user);
        } catch (MessageBagNotFoundException) {
            $messages = $this->initMessageBag();
        }

        $response = $this->chain->call($message, $messages);

        $messages[] = $message;
        $messages[] = Message::ofAssistant($response);
        $this->store->save($messages, $user);

        $this->respond($update->getChatId(), $response);
    }

    private function respond(int $chatId, string $response): void
    {
        $options = (new TelegramOptions())->chatId((string) $chatId);

        $this->chatter->send(
            (new ChatMessage($response))->options($options)
        );
    }

    private function initMessageBag(): MessageBag
    {
        $messages = new MessageBag(Message::forSystem(self::SYSTEM_PROMPT));
        $messages[] = Message::ofAssistant(self::ASSISTANT_INTRO);

        return $messages;
    }
}
