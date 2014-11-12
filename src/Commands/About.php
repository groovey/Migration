<?php namespace Groovey\Migration\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Groovey\Migration\Adapters\Adapter;

class About extends Command
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
            ->setName('migration:about')
            ->setDescription('Shows credits to the author.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

$about = <<<ABOUT

       ______
      / ____/________  ____ _   _____  __  __
     / / __/ ___/ __ \/ __ \ | / / _ \/ / / /
    / /_/ / /  / /_/ / /_/ / |/ /  __/ /_/ /
    \____/_/   \____/\____/|___/\___/\__, /
                                    /____/

    Project Name: Groovey Migration
    Git: https://github.com/groovey/migration
    Author: Harold Kim Cantil <pokoot@gmail.com>

    Show us your support by liking the project.

ABOUT;

        $output->writeln($about);
    }

}
