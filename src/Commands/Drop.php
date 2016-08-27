<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Groovey\Migration\Migration;
use Groovey\Support\Output;

class Drop extends Command
{
    private $adapter;

    public function __construct($app)
    {
        parent::__construct();

        $this->app = $app;
    }

    protected function configure()
    {
        $this
            ->setName('migrate:drop')
            ->setDescription('[Caution] Drops the migration table.')
        ;
    }

    private function drop()
    {
        $app   = $this->app;
        $query = 'DROP TABLE IF EXISTS `migrations`;';

        return $app['db']::statement($query);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = Output::style($output);
        $helper = $this->getHelper('question');

        $output->writeln('<warning>Warning!!!</warning>');

        $question = new ConfirmationQuestion(
            '<question>Migrations table will be deleted, are you sure you want to proceed? (y/N):</question> ',
            false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $this->drop();

        $text = '<highlight>Migrations table has been deleted.</highlight>';

        $output->writeln($text);
    }
}
