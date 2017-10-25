<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Groovey\Migration\Migration;
use Groovey\Support\Output;

class Reset extends Command
{
    private $app;

    public function __construct($app)
    {
        parent::__construct();
        $this->app = $app;
    }

    protected function configure()
    {
        $this
            ->setName('migrate:reset')
            ->setDescription('Truncates all migrations data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app    = $this->app;
        $helper = $this->getHelper('question');
        $output = Output::style($output);

        $output->writeln('<warning>Warning!!!</warning>');

        $question = new ConfirmationQuestion(
            '<question>All migration entries will be cleared, are you sure you want to proceed? (y/N):</question> ',
            false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $app['db']->table('migrations')->truncate();

        $text = '<highlight>All migration entries has been cleared.</highlight>';

        $output->writeln($text);
    }
}
