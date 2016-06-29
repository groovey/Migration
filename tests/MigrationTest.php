<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Illuminate\Database\Capsule\Manager as Capsule;
use Groovey\Migration\Commands\About;
use Groovey\Migration\Commands\Init;

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
        $app = new Application();
        $app->add(new About());

        $command = $app->find('migrate:about');

        $tester = new CommandTester($command);
        $tester->execute([
                'command' => $command->getName(),
            ]);

        $this->assertRegExp('/Groovey/', $tester->getDisplay());
    }

    public function testInit()
    {
        $app = new Application();
        $container['db'] = $this->connect();

        $app->add(new Init($container));
        $command = $app->find('migrate:init');

        $tester = new CommandTester($command);
        $tester->execute([
                'command' => $command->getName(),
            ]);

        print_r( $tester->getDisplay() );


        $this->assertRegExp('/Sucessfully/', $tester->getDisplay());
    }
}
