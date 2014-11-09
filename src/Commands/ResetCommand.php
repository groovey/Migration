<?php namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Groovey\Migration\Models\Migration;
use Groovey\Migration\Adapters\Adapter;

class ResetCommand extends Command
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
            ->setName('migration:reset')
            ->setDescription('Truncates all data.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->adapter->reset();

        $text = '<info>All datas has been cleared.</info>';

        $output->writeln($text);
    }
}