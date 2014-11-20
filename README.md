# Groovey Migration

A simple migration script tool that uses yaml file for native sql script. What it means is that there no more need for you to learn a new migration database language. Use `native sql language` code at your comfort.


## Usage

    $ groovey migrate:up

## Installation

Install using composer. To learn more about composer, visit: https://getcomposer.org/

```json
{
    "require": {
        "groovey/migration": "~1.2"
    }
}
```

Then run `composer.phar update`.

### The Groovey File

On your project root folder. Create a file called `groovey`. Or this could be any project name like `awesome`. Then copy the code below.

```php
#!/usr/bin/env php
<?php

set_time_limit(0);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/database.php';

use Symfony\Component\Console\Application;
use Groovey\Migration\Adapters\Adapter;
use Groovey\Migration\Adapters\Mysql;
use Groovey\Migration\Migration;
use Groovey\Migration\Commands;

$adapter   = new Adapter(new Mysql);
$migration = new Migration($adapter);
$app       = new Application;

$app->addCommands(
        $migration->getCommands()
    );

$status = $app->run();

exit($status);
```

### The Database Bootstrap File

Change the default parameters of the database to your environment settings.

```php
<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'groovey',
    'username'  => 'root',
    'password'  => 'awesome',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => ''
], 'default');

$capsule->bootEloquent();
$capsule->setAsGlobal();

return $capsule;
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

    $ groovey migrate:create Create A Test Table

The command will generate the formatted file like `001_create_a_test_table.yml`.

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

## List

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





























