<?php

declare(strict_types=1);

namespace App\Command;

use App\ChatBot\LlmChain\FunctionChain;
use App\ChatBot\LlmChain\Message\Message;
use App\ChatBot\LlmChain\Message\MessageBag;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:demo:functions', description: 'Command for testing function calls')]
final class DemoFunctionsCommand extends Command
{
    public function __construct(private readonly FunctionChain $chain)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Demo Functions');

        $prompt = $io->ask('What do you want to know?', 'Who is current chancellor of Germany?');
        $response = $this->chain->call(Message::ofUser($prompt), new MessageBag());

        $io->writeln($response);

        return 0;
    }
}
