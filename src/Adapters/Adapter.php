<?php namespace Groovey\Migration\Adapters;

class Adapter
{

    public $database;

    public function __construct($database)
    {
        $this->database = $database;

    }

    public function init()
    {
        $this->database->create();
    }

}
