<?php namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Groovey\Migration\Models\Migration;
use Groovey\Migration\Adapters\Adapter;

class Drop extends Command
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
            ->setName('migration:drop')
            ->setDescription('[Caution] Drops the migration table.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Migration table will be drop, are you sure you want to proceed? (Y/N): ', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $this->adapter->drop();

        $text = '<error>Migrations table gone.</error>';

        $output->writeln($text);
    }
}