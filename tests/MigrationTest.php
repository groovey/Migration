<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Groovey\Migration\Commands\About;
use Groovey\Migration\Commands\Init;
use Groovey\Migration\Commands\Reset;
use Groovey\Migration\Commands\Listing;
use Groovey\Migration\Commands\Status;
use Groovey\Migration\Commands\Up;
use Groovey\Migration\Commands\Down;
use Groovey\Migration\Commands\Drop;

class MigrationTest extends PHPUnit_Framework_TestCase
{
    public function connect()
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'test',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix'    => '',
        ], 'default');

        $capsule->bootEloquent();
        $capsule->setAsGlobal();

        return $capsule;
    }

    public function testAbout()
    {
        $tester = new Tester();
        $tester->command(new About(), 'migrate:about');
        $this->assertRegExp('/Groovey/', $tester->getDisplay());
    }

    public function testInit()
    {
        $container['db'] = $this->connect();

        $tester = new Tester();
        $tester->command(new Init($container), 'migrate:init');
        $this->assertRegExp('/Sucessfully/', $tester->getDisplay());
    }

    public function testReset()
    {
        $container['db'] = $this->connect();

        $tester = new Tester();
        $tester->command(new Reset($container), 'migrate:reset', 'Y\n');

        $this->assertRegExp('/All migration entries has been cleared/',
                $tester->getDisplay());
    }

    public function testStatus()
    {
        $container['db'] = $this->connect();

        $tester = new Tester();
        $tester->command(new Status($container), 'migrate:status');
        $this->assertRegExp('/Unmigrated SQL/', $tester->getDisplay());
        $this->assertRegExp('/001_users.yml/', $tester->getDisplay());
    }

    public function testUp()
    {
        $container['db'] = $this->connect();

        $tester = new Tester();
        $tester->command(new Up($container), 'migrate:up');

        $this->assertRegExp('/Running migration file/', $tester->getDisplay());
    }

    public function testListing()
    {
        $container['db'] = $this->connect();

        $tester = new Tester();
        $tester->command(new Listing($container), 'migrate:list');
        $this->assertRegExp('/Id | Version/', $tester->getDisplay());
        $this->assertRegExp('/1  | 001/', $tester->getDisplay());
    }

    public function testDown()
    {
        $container['db'] = $this->connect();

        $tester = new Tester();
        $tester->command(new Down($container), 'migrate:down', 'Y\n');

        $this->assertRegExp('/Downgrading migration file/', $tester->getDisplay());
    }
    public function testDrop()
    {
        $container['db'] = $this->connect();

        $tester = new Tester();
        $tester->command(new Drop($container), 'migrate:drop', 'Y\n');
        $this->assertRegExp('/Migrations table has been deleted/', $tester->getDisplay());
    }
}
