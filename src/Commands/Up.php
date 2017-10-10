<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Yaml\Parser;
use Groovey\Migration\Migration;
use Groovey\Support\Output;

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
            ->setDescription('Runs the migration up script.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app    = $this->app;
        $yaml   = new Parser();
        $output = Output::style($output);
        $helper = $this->getHelper('question');
        $dir    = Migration::getDirectory();
        $files  = Migration::getUnMigratedFiles($app);
        $total  = count($files);

        if ($total == 0) {
            $output->writeln('<error>No new files to be migrated.</error>');

            return;
        }

        $output->writeln('<highlight>Migration will run the following files:</highlight>');

        foreach ($files as $file) {
            $output->writeln("<info>- $file</info>");
        }

        $question = new ConfirmationQuestion('<question>Are you sure you want to proceed? (Y/n):</question> ', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        foreach ($files as $file) {
            $output->writeln("<info>- Migrating ($file)</info>");

            $content    = $yaml->parse(file_get_contents($dir.'/'.$file));
            $up         = explode(';', trim($content['up']));
            $up         = array_filter($up);
            $date       = element('date', $content);
            $author     = element('author', $content);
            $changelog  = element('changelog', $content);
            $dateFormat = validate_date($date);

            if (!$dateFormat) {
                $output->writeln('<error>Invalid date (YYYY-mm-dd HH:mm:ss).</error>');

                return;
            } elseif (!$author) {
                $output->writeln('<error>Invalid author.</error>');

                return;
            } elseif (!$changelog) {
                $output->writeln('<error>Invalid changelog.</error>');

                return;
            }

            foreach ($up as $query) {
                $app['db']::statement(trim($query));
            }

            $info = Migration::getFileInfo($file);

            $major = $info['major'];
            $minor = $info['minor'];
            $build = $info['build'];

            $app['db']->table('migrations')->insert([
                    'version'    => $major.'.'.$minor.'.'.$build,
                    'author'     => $author,
                    'changelog'  => $changelog,
                    'created_at' => $date,
                    'updated_at' => new \DateTime(),
                ]);
        }
    }
}
