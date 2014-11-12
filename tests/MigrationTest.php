<?php

use Groovey\Migration\Migration;
use Groovey\Migration\Adapters\Mysql;
use Groovey\Migration\Adapters\Adapter;

class MigrationTest extends PHPUnit_Framework_TestCase
{
    public function testSatus()
    {
        $adapter     = new Adapter(new Mysql);
        $application = new Migration($adapter);

        $this->assertTrue(true);
    }

}
