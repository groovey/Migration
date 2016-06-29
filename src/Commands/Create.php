<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Groovey\Migration\Migration;

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
            ->setDescription('Creates a .yml migration file.')
            ->addArgument(
                'param',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'The migration task description.'
            )
        ;
    }

    private function getArguments(InputInterface $input)
    {
        $argument = '';

        if ($names = $input->getArgument('param')) {
            $argument .= implode('_', $names);
        }

        return trim($argument);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = Migration::getDirectory();
        $filename  = Migration::getGeneratedFilename($this->getArguments($input));
        $data      = Migration::getTemplate();

        file_put_contents($directory.'/'.$filename, $data);

        $text = '<info>Sucessfully created migration a file.</info>';
        $output->writeln($text);
    }
}
