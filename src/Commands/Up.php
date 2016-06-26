<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use Groovey\Migration\Migration;

class Up extends Command
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
            ->setName('migrate:up')
            ->setDescription('Run the migration.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app     = $this->app;
        $yaml    = new Parser();
        $dir     = Migration::getDirectory();

        $files = [];
        foreach (Migration::getUnMigratedFiles($app) as $file) {
            $output->writeln("<info>Running migration file ($file).</info>");

            $value = $yaml->parse(file_get_contents($dir.'/'.$file));

            $up = explode(';', trim($value['UP']));
            $up = array_filter($up);

            foreach ($up as $query) {
                $app['db']->executeQuery(trim($query));
            }

            $info = Migration::getFileInfo($file);

            $app['db']->insert('migrations', [
                    'version'     => $info['version'],
                    'description' => $info['description'],
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);

            $files[] = [$file];
        }
    }
}
