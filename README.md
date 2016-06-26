# Groovey Migration

A simple migration script tool that uses yaml file for native sql script. What it means is that there no more need for you to learn a new migration database language. Use `native sql language` code at your comfort.


## Usage

    $ groovey migrate:up

## Step 1 - Installation

Install using composer. To learn more about composer, visit: https://getcomposer.org/

    $ composer require groovey/migration

Then run `composer.phar update`.

## Step 2 - The Groovey File

On your project root folder. Create a file called `groovey`.

```php
#!/usr/bin/env php
<?php

set_time_limit(0);

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Groovey\Migration\Commands\Migration;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

$db = [
        'dbname'   => 'migration',
        'user'     => 'root',
        'password' => 'webdevel',
        'host'     => 'localhost',
        'driver'   => 'pdo_mysql',
    ];

$config = new Configuration();
$conn   = DriverManager::getConnection($db, $config);
$app    = new Application();

$container['db'] = $conn;

$app->addCommands([
        new Groovey\Migration\Commands\About(),
        new Groovey\Migration\Commands\Init($container),
        new Groovey\Migration\Commands\Reset($container),
        new Groovey\Migration\Commands\Listing($container),
        new Groovey\Migration\Commands\Drop($container),
        new Groovey\Migration\Commands\Status($container),
        new Groovey\Migration\Commands\Create($container),
        new Groovey\Migration\Commands\Up($container),
        new Groovey\Migration\Commands\Down($container),
    ]);

$status = $app->run();

exit($status);
```
 
Good job! Your now ready to discover the painless way of doing migrations.

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

    $ groovey migrate:create create_test

The command will generate the formatted file like `001_create_test.yml`.

## The YML file

This is where you store all your SQL scripts.

`NOTE: Tabs needs to be converted to spaces. This is the rule for YML files.`

For more information about YML files please visit: http://www.yaml.org/start.html

`NOTE: All sql statement has to end with a semicolon (;)`

Sample .yml file:

```yml
# Author: Name <your@email.com>

# Run the migration
UP: >

    SELECT 1;

    CREATE TABLE IF NOT EXISTS `test` (
      `id` int(11) NOT NULL,
      `name` int(11) NOT NULL,
      `created_at` int(11) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


# Reverse the migration
DOWN: >

    DROP TABLE test;
```

## Status

Running this command will check all the unmigrated yaml files.

    $ groovey migrate:status

Sample output:

```html
+-----------------------------+
| Unmigrated SQL              |
+-----------------------------+
| 001_create_a_test_table.yml |
+-----------------------------+
```

## Up

Runs the migration `UP` script.

    $ groovey migrate:up


Sample output:

    Running migration file (001_create_a_test_table.yml).

## Step 8 - List

Shows all the migrated yml scripts.

    $ groovey migrate:list


Sample output:

```text
+----+---------+---------------------+---------------------+
| id | Version | Description         | Created At          |
+----+---------+---------------------+---------------------+
| 1  | 001     | create a test table | 2014-11-12 16:07:16 |
+----+---------+---------------------+---------------------+
```


## Down

Reverse the last migration.

    $ groovey migrate:down

Reverse a specific migration version.

    $ groovey migrate:down 001

Sample output:

    Downgrading migration file (001_create_a_test_table.yml).


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

## Like us.

Give a `star` to show your support and love for the project.

## Contribution

Fork `Groovey Migration` and send us some issues.





























