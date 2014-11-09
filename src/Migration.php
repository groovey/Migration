<?php namespace Groovey\Migration;

use Symfony\Component\Console\Application;
use Groovey\Migration\Adapters\Adapter;
use Groovey\Migration\Commands\InitCommand;
use Groovey\Migration\Commands\ResetCommand;
use Groovey\Migration\Commands\ListCommand;
use Groovey\Migration\Commands\DropCommand;

class Migration extends Application
{
    public $adapter;

    public function __construct(Adapter $adapter)
    {
        parent::__construct();

        $this->add(new InitCommand($adapter));
        $this->add(new ResetCommand($adapter));
        $this->add(new ListCommand($adapter));
        $this->add(new DropCommand($adapter));
    }

}
