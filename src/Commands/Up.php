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

            $content     = $yaml->parse(file_get_contents($dir.'/'.$file));
            $up          = explode(';', trim($content['up']));
            $up          = array_filter($up);
            $date        = element('date', $content);
            $author      = element('author', $content);
            $description = element('description', $content);
            $valid       = validate_date($date);

            if (!$valid) {
                $output->writeln('<error>Invalid date (YY-mm-dd HH:mm:ss).</error>');
                exit();
            } elseif (!$author) {
                $output->writeln('<error>Invalid author.</error>');
                exit();
            } elseif (!$description) {
                $output->writeln('<error>Invalid description.</error>');
                exit();
            }

            foreach ($up as $query) {
                $app['db']::statement(trim($query));
            }

            $info = Migration::getFileInfo($file);

            $app['db']->table('migrations')->insert([
                    'filename'    => $file,
                    'version'     => $info['version'],
                    'author'      => $author,
                    'description' => $description,
                    'created_at'  => $date,
                    'updated_at'  => new \DateTime(),
                ]);

            $files[] = [$file];
        }
    }
}
