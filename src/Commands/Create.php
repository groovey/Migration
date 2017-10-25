<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Groovey\Migration\Migration;
use Groovey\Support\Output;

class Create extends Command
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
            ->setName('migrate:create')
            ->setDescription('Creates a .yml migration file')
            ->addArgument(
                'version',
                InputArgument::REQUIRED,
                'The version in {major.minor.build} format.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app       = $this->app;
        $output    = Output::style($output);
        $helper    = $this->getHelper('question');
        $directory = Migration::getDirectory();
        $data      = Migration::getTemplate();
        $version   = $input->getArgument('version');
        $filename  = $version.'.yml';
        $question  = new ConfirmationQuestion('<question>Are you sure you want to proceed? (Y/n):</question> ', false);

        if (!Migration::validateVersion($version)) {
            $output->writeln('<error>Invalid version format (major.minor.build). </error>');

            return;
        }

        $output->writeln('<highlight>Creating migration file:</highlight>');
        $output->writeln("<info> - $filename</info>");

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        if (file_exists($directory.'/'.$filename)) {
            $output->writeln("<error>The migration file already $filename exists.</error>");

            return;
        }

        file_put_contents($directory.'/'.$filename, $data);

        $text = '<info>Sucessfully created migration file.</info>';
        $output->writeln($text);
    }
}
