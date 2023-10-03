<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain;

use App\ChatBot\LlmChain\Message\Message;
use App\ChatBot\LlmChain\Message\MessageBag;
use App\ChatBot\LlmChain\OpenAI\ChatModel;

final readonly class ChatChain implements LlmChainInterface
{
    public function __construct(private ChatModel $model)
    {
    }

    public function call(Message $message, MessageBag $messages): string
    {
        $response = $this->model->call($messages->with($message));

        return $response['choices'][0]['message']['content'];
    }
}
