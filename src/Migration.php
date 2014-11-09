<?php namespace Groovey\Migration;

use Symfony\Component\Console\Application;
use Groovey\Migration\Commands\InitCommand;
use Groovey\Migration\Adapters\Adapter;

class Migration extends Application
{
    public $adapter;

    public function __construct(Adapter $adapter)
    {
        parent::__construct();

        $this->add(new InitCommand($adapter));
    }

}
