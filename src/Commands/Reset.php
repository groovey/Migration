<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
            ->setDescription('Truncates all migrations data.')
        ;
    }

    private function truncate()
    {
        $app = $this->app;
        $sql = 'TRUNCATE TABLE `migrations`';

        return $app['db']->executeQuery($sql);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new ConfirmationQuestion(
            '<question>All datas will be truncated, are you sure you want to proceed? (y/N):</question> ',
            false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $this->truncate();

        $text = '<info>All datas has been cleared.</info>';

        $output->writeln($text);
    }
}
