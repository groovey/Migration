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

    private function getList()
    {
        $app   = $this->app;
        $query = 'SELECT * FROM migrations ORDER BY version ASC';

        return $app['db']->fetchAll($query);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrations = $this->getList();

        $datas = [];

        foreach ($migrations as $migration) {
            $datas[] = [
                'id'          => $migration['id'],
                'version'     => $migration['version'],
                'description' => $migration['description'],
                'created at'  => $migration['created_at'],
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['id', 'Version', 'Description', 'Created At'])
            ->setRows($datas);

        $table->render();
    }
}
