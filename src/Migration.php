<?php namespace Groovey\Migration;

use Symfony\Component\Console\Application;
use Groovey\Migration\Adapters\Adapter;
use Groovey\Migration\Commands\Init;
use Groovey\Migration\Commands\Reset;
use Groovey\Migration\Commands\Listing;
use Groovey\Migration\Commands\Drop;
use Groovey\Migration\Commands\Create;
use Groovey\Migration\Commands\Up;

class Migration extends Application
{
    public $adapter;

    public function __construct(Adapter $adapter)
    {
        parent::__construct();

        $this->add(new Init($adapter));
        $this->add(new Reset($adapter));
        $this->add(new Listing($adapter));
        $this->add(new Drop($adapter));
        $this->add(new Create($adapter));
        $this->add(new Up($adapter));
    }

}
