<?php namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Groovey\Migration\Models\Migration;
use Groovey\Migration\Adapters\Adapter;

class InitCommand extends Command
{
    private $adapter;

    public function __construct(Adapter $adapter)
    {
        parent::__construct();

        $this->adapter = $adapter;
    }

    protected function configure()
    {
        $this
            ->setName('migration:init')
            ->setDescription('Setup your directory and creates a migration database table.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->adapter->init();

        $folder = getcwd() . '/database/migrations';

        if (false === @mkdir($folder, 0755, true) && !file_exists($folder)) {
            $output->writeln("<info>Unable to create folder. Create file permissions.</info>");

            return;
        }

        if (file_exists($folder) && is_dir($folder)) {
            $output->writeln("<error>Place all your migration files in ($folder)</error>");
        }

        $text = '<info>Sucessfully created migrations database.</info>';

        $output->writeln($text);
    }
}
