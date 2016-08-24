<?php

namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Groovey\Migration\Migration;

class Create extends Command
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
            ->setName('migrate:create')
            ->setDescription('Creates a .yml migration file.')
            ->addArgument(
                'description',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'The migration task description.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app         = $this->app;
        $description = $input->getArgument('description');
        $directory   = Migration::getDirectory();
        $data        = Migration::getTemplate();
        $underscore  = implode('_', $description);
        $version     = Migration::getNextVersion($app);
        $filename    = $version.'_'.$underscore.'.yml';
        $helper      = $this->getHelper('question');
        $question    = new ConfirmationQuestion('<question>Are you sure you want to proceed? (Y/n):</question> ', false);

        $output->writeln("<info>This will create a file ($filename)</info>");

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        if (file_exists($directory.'/'.$filename)) {
            $output->writeln("<error>The migration file already $filename exists.</error>");
            exit();
        }

        file_put_contents($directory.'/'.$filename, $data);

        $text = "<info>Sucessfully created migration ($filename).</info>";
        $output->writeln($text);
    }
}
