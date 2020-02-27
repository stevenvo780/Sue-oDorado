<?php
declare (strict_types = 1);

namespace App\Command;

use App\Controller\Server\Chat;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

//Ratchet libraries
use Symfony\Component\Console\Output\OutputInterface;

class ChatServer extends Command
{
    protected static $defaultName = 'chat:start';

    protected function configure()
    {
        $this->setName('chat:start')
            ->setDescription('Starts chat server');

        /* Debug Mode
    $this
    ->setDescription('Starts the websocket chat')
    ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
    ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
    ;
     */
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* Debug Mode
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
        $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
        // ...
        }

        $io->success('Chat has started successfully');

        $output->writeln([
        'Websocket chat',
        '============',
        'Starting chat, open your browser.',
        ]);

        $server = IoServer::factory(new HttpServer(new WsServer(new Chat())),8080);

        $server->run();
         */

        $chatServer = IoServer::factory(new HttpServer(new WsServer(new Chat())), 8080);
        $chatServer->run();

    }
}
