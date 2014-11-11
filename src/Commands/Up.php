<?php namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use Illuminate\Database\Capsule\Manager as DB;
use Groovey\Migration\Models\Migration;
use Groovey\Migration\Adapters\Adapter;
use Groovey\Migration\Manager;

class Up extends Command
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
            ->setName('migration:up')
            ->setDescription('Run the migration.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $yaml = new Parser();

        $dir = Manager::getDirectory();

        $files = [];
        foreach (Manager::getUnMigratedFiles() as $file) {

            $output->writeln("Running migration file ($file).");

            $value = $yaml->parse(file_get_contents($dir . '/' . $file));

            $up = explode(';', trim($value['UP']));
            $up = array_filter($up);

            foreach ($up as $query) {
                DB::statement(trim($query));
            }

            $info = Manager::getFileInfo($file);

            $entry              = new Migration();
            $entry->version     = $info['version'];
            $entry->description = $info['description'];
            $entry->created_at  = new \DateTime();
            $entry->save();

            $files[] = [$file];
        }

    }

}
