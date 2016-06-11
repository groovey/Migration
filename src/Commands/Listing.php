<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Groovey\Migration\Models\Migration;
use Groovey\Migration\Adapters\Adapter;

class Listing extends Command
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
            ->setName('migrate:list')
            ->setDescription('Listing off all the migrated script.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrations = Migration::orderBy('version')->get();

        $datas = [];

        foreach ($migrations as $migration) {
            $datas[] = [
                'id'          => $migration->id,
                'version'     => $migration->version,
                'description' => $migration->description,
                'created at'  => $migration->created_at,
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['id', 'Version', 'Description', 'Created At'])
            ->setRows($datas);

        $table->render();
    }
}
