<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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

        return $app['db']->executeQuery($query);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new ConfirmationQuestion(
            '<question>Migrations table will be drop, are you sure you want to proceed? (y/N):</question> ',
            false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $this->drop();

        $text = '<info>Migrations table is now gone.</info>';

        $output->writeln($text);
    }
}
