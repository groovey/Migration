<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Yaml\Parser;
use Groovey\Migration\Migration;
use Groovey\Migration\Models\Migration as Migrations;
use Illuminate\Database\Capsule\Manager as DB;

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
            $record = Migrations::where('version', '=', $param)->first();

            if (!$record) {
                $output->writeln('<error>Unable to find migration version.</error>');

                return;
            }

            $records = Migrations::where('id', '>=', $record->id)
                            ->orderBy('version', 'DESC')
                            ->get();
        } else {
            $records = Migrations::orderBy('version', 'DESC')->take(1)->get();
        }

        foreach ($records as $record) {
            $id          = $record->id;
            $version     = $record->version;
            $description = $record->description;

            $file = $version.'_'.str_replace(' ', '_', $description).'.yml';

            $output->writeln("<info>Downgrading migration file ($file).</info>");

            $value = $yaml->parse(file_get_contents($dir.'/'.$file));

            $down  = explode(';', trim($value['DOWN']));
            $down  = array_filter($down);

            foreach ($down as $query) {
                DB::statement(trim($query));
            }

            $data = Migrations::find($id);
            $data->delete();
        }
    }
}
