<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Yaml\Parser;
use Groovey\Migration\Migration;

class Down extends Command
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
            ->setName('migrate:down')
            ->setDescription('Reverese the migration.')
            ->addArgument(
                'param',
                InputArgument::OPTIONAL,
                'Version number to rollback'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app    = $this->app;
        $dir    = Migration::getDirectory();
        $yaml   = new Parser();
        $param  = $input->getArgument('param');
        $helper = $this->getHelper('question');

        $question = new ConfirmationQuestion(
            '<question>Are you sure you want to proceed? (y/N):</question> ',
            false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        if ($param) {
            $record = $app['db']->fetchAssoc("SELECT * FROM migrations WHERE version = $param LIMIT 1");

            if (!$record) {
                $output->writeln('<error>Unable to find migration version.</error>');

                return;
            }

            $id = $record['id'];

            $query   = "SELECT * FROM migrations WHERE id >= {$id} ORDER BY version DESC";
            $records = $app['db']->fetchAll($query);
        } else {
            $records = $app['db']->fetchAll('SELECT * FROM migrations ORDER BY version DESC LIMIT 1');
        }

        foreach ($records as $record) {
            $id          = $record['id'];
            $version     = $record['version'];
            $description = $record['description'];

            $file = $version.'_'.str_replace(' ', '_', $description).'.yml';

            $output->writeln("<info>Downgrading migration file ($file).</info>");

            $value = $yaml->parse(file_get_contents($dir.'/'.$file));

            $down  = explode(';', trim($value['DOWN']));
            $down  = array_filter($down);

            foreach ($down as $query) {
                $app['db']->executeQuery(trim($query));
            }

            $app['db']->delete('migrations', ['id' => $id]);
        }
    }
}
