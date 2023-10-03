<?php

declare(strict_types=1);

namespace App\ChatBot\LlmChain;

use App\ChatBot\LlmChain\Message\Message;
use App\ChatBot\LlmChain\Message\MessageBag;
use App\ChatBot\LlmChain\OpenAI\ChatModel;
use App\ChatBot\LlmChain\ToolBox\FunctionRegistry;

final readonly class FunctionChain implements LlmChainInterface
{
    public function __construct(
        private ChatModel $model,
        private FunctionRegistry $functionRegistry,
    ) {
    }

    public function call(Message $message, MessageBag $messages): string
    {
        $messages[] = $message;

        $response = $this->model->call($messages, [
            'functions' => $this->functionRegistry->getMap(),
        ]);

        while ('function_call' === $response['choices'][0]['finish_reason']) {
            ['name' => $name, 'arguments' => $arguments] = $response['choices'][0]['message']['function_call'];
            $result = $this->functionRegistry->execute($name, $arguments);

            $messages[] = Message::ofAssistant(functionCall: [
                'name' => $name,
                'arguments' => $arguments,
            ]);
            $messages[] = Message::ofFunctionCall($name, $result);

            $response = $this->model->call($messages);
        }

        return $response['choices'][0]['message']['content'];
    }
}
