<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Illuminate\Database\Capsule\Manager as Capsule;
use Groovey\Migration\Commands\About;
use Groovey\Migration\Commands\Init;
use Groovey\Migration\Commands\Reset;

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

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
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

        $this->assertRegExp('/Sucessfully/', $tester->getDisplay());
    }

    public function testReset()
    {
        $app = new Application();
        $container['db'] = $this->connect();

        $app->add(new Reset($container));
        $command = $app->find('migrate:reset');

        $tester = new CommandTester($command);
        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("Y\n"));

        $tester->execute([
                'command' => $command->getName(),
            ]);

        $this->assertRegExp('/All migration entries has been cleared/',
                $tester->getDisplay());
    }
}
