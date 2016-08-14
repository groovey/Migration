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
                'version',
                InputArgument::REQUIRED,
                'The migration file.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version   = $input->getArgument('version');
        $directory = Migration::getDirectory();
        $data      = Migration::getTemplate();
        $filename  = $version.'.yml';

        if (file_exists($directory.'/'.$filename)) {
            $output->writeln("<error>The migration file already $filename exists.</error>");
            exit();
        }

        file_put_contents($directory.'/'.$filename, $data);

        $text = "<info>Sucessfully created migration ($filename).</info>";
        $output->writeln($text);
    }
}
