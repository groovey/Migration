<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Groovey\Migration\Migration;
use Groovey\Support\Output;

class Init extends Command
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
            ->setName('migrate:init')
            ->setDescription('Setup the migrations directory and database.')
        ;
    }

    private function init()
    {
        $app = $this->app;

        $query = '
                CREATE TABLE IF NOT EXISTS `migrations` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `version` char(3) COLLATE utf8_unicode_ci NOT NULL,
                  `author` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
                  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                  `changelog` text COLLATE utf8_unicode_ci NOT NULL,
                  `created_at` datetime NOT NULL,
                  `updated_at` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
            ';

        return $app['db']::statement($query);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init();

        $output = Output::style($output);
        $folder = getcwd().'/database/migrations';

        if (false === @mkdir($folder, 0755, true) && !file_exists($folder)) {
            $output->writeln('<error>Unable to create folder. Check file permissions.</error>');

            return;
        }

        if (file_exists($folder) && is_dir($folder)) {
            $output->writeln('<highlight>Sucessfully created migrations database folder.</highlight>');
            $output->writeln('<info>Place all your migration files in:</info>');
            $output->writeln("<comment>$folder</comment>");
        }
    }
}
