# Migration

Groovey Migration Package

## Usage

    $ groovey migrate:up

## Installation

    $ composer require groovey/migration


## Setup

On your project root folder. Create a file called `groovey`.

```php
#!/usr/bin/env php
<?php

set_time_limit(0);

require_once __DIR__.'/vendor/autoload.php';

use Silex\Application;
use Groovey\Console\Providers\ConsoleServiceProvider;
use Groovey\DB\Providers\DBServiceProvider;

$app = new Application();

$app->register(new ConsoleServiceProvider(), [
        'console.name'    => 'Groovey',
        'console.version' => '1.0.0',
    ]);

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

$console = $app['console'];

$console->addCommands([
        new Groovey\Migration\Commands\About(),
        new Groovey\Migration\Commands\Init($app),
        new Groovey\Migration\Commands\Reset($app),
        new Groovey\Migration\Commands\Listing($app),
        new Groovey\Migration\Commands\Drop($app),
        new Groovey\Migration\Commands\Status($app),
        new Groovey\Migration\Commands\Create($app),
        new Groovey\Migration\Commands\Up($app),
        new Groovey\Migration\Commands\Down($app),
    ]);

$status = $console->run();

exit($status);
```

## List of Commands

- [Init](#init)
- [Create](#create)
- [Status](#status)
- [Up](#up)
- [List](#list)
- [Down](#down)
- [Reset](#reset)
- [Drop](#drop)
- [About](#about)

## Init

Setup your migration directory relative to your root folder `./database/migrations`.

    $ groovey migrate:init

## Create

Automatically create the yaml file.

    $ groovey migrate:create 001

The command will generate a formatted file like `001.yml`.

## The YML file

Sample 001.yml file:

```yml
date: 'YYYY-mm-dd HH:mm:ss'
author: Groovey
changelog: >

up: >

    SELECT 1;

    CREATE TABLE IF NOT EXISTS `test` (
      `id` int(11) NOT NULL,
      `name` int(11) NOT NULL,
      `created_at` int(11) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

down: >

    DROP TABLE test;
```

## Status

Running this command will check all the unmigrated yaml files.

    $ groovey migrate:status

Sample output:

```text
+----------------+
| Unmigrated YML |
+----------------+
| 001.yml        |
+----------------+
```

## Up

Runs the migration `up` script.

    $ groovey migrate:up


Sample output:

    Running migration file (001.yml).

## List

Shows all the migrated yml scripts.

    $ groovey migrate:list

Sample output:

```text
+---------+------------+------------------------------------------+------------+------------+
| Version | Author     | Changelog                                | Created At | Updated At |
+---------+------------+------------------------------------------+------------+------------+
| 001     | Groovey    | User information.                        | 2016-06-26 | 2016-08-14 |
| 002     | Pokoot     | Sample select.                           | 2016-08-14 | 2016-08-14 |
+---------+------------+------------------------------------------+------------+------------+
```

## Down

Revert to the last migration.

    $ groovey migrate:down

Reverse a specific migration version.

    $ groovey migrate:down 001

Sample output:

    Downgrading migration file (001.yml).

## Reset

Truncates all migrated records.

    $ groovey migrate:reset

Sample output:

    All datas will be truncated, are you sure you want to proceed? (Y/N): Y
    All datas has been cleared.

## Drop

Drops the `migrations` table.

    $ groovey migrate:drop

Sample output:

    Migration table will be drop, are you sure you want to proceed? (Y/N): Y
    Migrations table gone.

## About

Shows the library information details.

    $ groovey migrate:about