<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
            ->setDescription('Runs the migration up script.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app    = $this->app;
        $yaml   = new Parser();
        $dir    = Migration::getDirectory();
        $files  = Migration::getUnMigratedFiles($app);
        $output = Migration::outputFormatter($output);
        $total  = count($files);
        $helper = $this->getHelper('question');
        $list   = implode(',', $files);

        if ($total == 0) {
            $output->writeln('<error>No new files to be migrated.</error>');
            exit();
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
            $output->writeln("<info>- Migrating ($file).</info>");

            $content    = $yaml->parse(file_get_contents($dir.'/'.$file));
            $up         = explode(';', trim($content['up']));
            $up         = array_filter($up);
            $date       = element('date', $content);
            $author     = element('author', $content);
            $changelog  = element('changelog', $content);
            $dateFormat = validate_date($date);
            $fileFormat = Migration::validateFileFormat($file);

            if (!$fileFormat) {
                $output->writeln('<error>Invalid file format.</error>');
                exit();
            } elseif (!$dateFormat) {
                $output->writeln('<error>Invalid date (YYYY-mm-dd HH:mm:ss).</error>');
                exit();
            } elseif (!$author) {
                $output->writeln('<error>Invalid author.</error>');
                exit();
            } elseif (!$changelog) {
                $output->writeln('<error>Invalid changelog.</error>');
                exit();
            }

            foreach ($up as $query) {
                $app['db']::statement(trim($query));
            }

            $info = Migration::getFileInfo($file);

            $app['db']->table('migrations')->insert([
                    'version'    => $info['version'],
                    'author'     => $author,
                    'filename'   => $file,
                    'changelog'  => $changelog,
                    'created_at' => $date,
                    'updated_at' => new \DateTime(),
                ]);
        }
    }
}
