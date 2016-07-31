<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class Listing extends Command
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
            ->setName('migrate:list')
            ->setDescription('Listing off all the migrated script.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->app;
        $migrations = $app['db']->table('migrations')->orderBy('version')->get();

        $datas = [];

        foreach ($migrations as $migration) {
            $datas[] = [
                'id'          => $migration->id,
                'version'     => $migration->version,
                'author'      => $migration->author,
                'description' => $migration->description,
                'created at'  => $migration->created_at,
                'updated at'  => $migration->updated_at,
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Id', 'Version', 'Author', 'Description', 'Created At', 'Updated At'])
            ->setRows($datas);

        $table->render();
    }
}
