<?php

namespace Groovey\Migration;

use Groovey\Migration\Adapters\Adapter;

class Migration
{
    public $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getCommands()
    {
        $adapter = $this->adapter;

        return [
            new Commands\Init($adapter),
            new Commands\Reset($adapter),
            new Commands\Listing($adapter),
            new Commands\Drop($adapter),
            new Commands\Status($adapter),
            new Commands\Create($adapter),
            new Commands\Up($adapter),
            new Commands\Down($adapter),
            new Commands\Up($adapter),
            new Commands\About(),
        ];
    }
}
