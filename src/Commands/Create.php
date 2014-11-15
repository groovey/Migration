<?php namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Groovey\Migration\Manager;
use Groovey\Migration\Models\Migration;
use Groovey\Migration\Adapters\Adapter;

class Create extends Command
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
            ->setName('migrate:create')
            ->setDescription('Creates a .yml migration file under database/migrations.')
            ->addArgument(
                'param',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'The database task description.'
            )
        ;
    }

    private function getArguments(InputInterface $input)
    {
        $argument = '';

        if ($names = $input->getArgument('param')) {
            $argument .= implode('_', $names);
        }

        return trim($argument);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $directory = Manager::getDirectory();
        $filename  = Manager::getGeneratedFilename($this->getArguments($input));
        $data      = Manager::getTemplate();

        file_put_contents($directory . '/' . $filename, $data);

        $text = '<info>Sucessfully created migration file.</info>';
        $output->writeln($text);
    }

}
