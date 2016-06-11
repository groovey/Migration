<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Yaml\Parser;
use Illuminate\Database\Capsule\Manager as DB;
use Groovey\Migration\Models\Migration;
use Groovey\Migration\Adapters\Adapter;
use Groovey\Migration\Manager;

class Down extends Command
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
        $dir    = Manager::getDirectory();
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
            $record = Migration::where('version', '=', $param)->first();

            if (!$record) {
                $output->writeln('<error>Unable to find migration version.</error>');

                return;
            }

            $records = Migration::where('id', '>=', $record->id)
                            ->orderBy('version', 'DESC')
                            ->get();
        } else {
            $records = Migration::orderBy('version', 'DESC')->take(1)->get();
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

            $data = Migration::find($id);
            $data->delete();
        }
    }
}
