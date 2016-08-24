<?php

use Silex\Application;
use Groovey\DB\Providers\DBServiceProvider;
use Groovey\Tester\Providers\TesterServiceProvider;
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
    public $app;

    public function setUp()
    {
        $app = new Application();
        $app['debug'] = true;

        $app->register(new TesterServiceProvider());

        $app->register(new DBServiceProvider(), [
            'db.connection' => [
                'host'      => 'localhost',
                'driver'    => 'mysql',
                'database'  => 'test_migration',
                'username'  => 'root',
                'password'  => '',
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
                'logging'   => true,
            ],
        ]);

        $app['tester']->add([
                new About(),
                new Init($app),
                new Reset($app),
                new Status($app),
                new Up($app),
                new Listing($app),
                new Down($app),
                new Drop($app),
            ]);

        $this->app = $app;
    }

    public function testAbout()
    {
        $app = $this->app;
        $display = $app['tester']->command('migrate:about')->execute()->display();
        $this->assertRegExp('/Groovey/', $display);
    }

    public function testInit()
    {
        $app = $this->app;
        $display = $app['tester']->command('migrate:init')->execute()->display();
        $this->assertRegExp('/Sucessfully/', $display);
    }

    public function testReset()
    {
        $app = $this->app;
        $display = $app['tester']->command('migrate:reset')->input('Y\n')->execute()->display();
        $this->assertRegExp('/All migration entries has been cleared/', $display);
    }

    public function testStatus()
    {
        $app = $this->app;
        $display = $app['tester']->command('migrate:status')->execute()->display();
        $this->assertRegExp('/Unmigrated YML/', $display);
        $this->assertRegExp('/001/', $display);
        $this->assertRegExp('/002/', $display);
    }

    public function testUp()
    {
        $app = $this->app;
        $display = $app['tester']->command('migrate:up')->input('Y\n')->execute()->display();
        $this->assertRegExp('/001/', $display);
        $this->assertRegExp('/002/', $display);
    }

    public function testListing()
    {
        $app = $this->app;
        $display = $app['tester']->command('migrate:list')->execute()->display();
        $this->assertRegExp('/Version/', $display);
        $this->assertRegExp('/001/', $display);
        $this->assertRegExp('/002/', $display);
    }

    public function testDown()
    {
        $app = $this->app;
        $display = $app['tester']->command('migrate:down')->input('Y\n')->execute(['version' => '001'])->display();
        $this->assertRegExp('/Downgrading/', $display);
    }

    public function testDrop()
    {
        $app = $this->app;
        $display = $app['tester']->command('migrate:drop')->input('Y\n')->execute()->display();
        $this->assertRegExp('/Migrations table has been deleted/', $display);
    }
}
