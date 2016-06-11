<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Groovey\Migration\Adapters\Adapter;
use Groovey\Migration\Manager;

class Status extends Command
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
            ->setName('migrate:status')
            ->setDescription('List all the migrations file that have not been migrated.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = [];
        foreach (Manager::getUnMigratedFiles() as $file) {
            $files[] = [$file];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Unmigrated SQL'])
            ->setRows($files)
        ;
        $table->render();
    }
}
