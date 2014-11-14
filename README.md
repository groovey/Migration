# Groovey Migration

A simple migration script tool that uses yaml file for native sql script. What it means is that there no more need for you to learn a new migration database language. Use **`native sql language`** code at your comfort.


## Usage

    $ groovey migration:up

## Installation

Install using composer. To learn more about composer, visit: https://getcomposer.org/

```json
{
    "require": {
        "groovey/migration": "~1.0"
    }
}
```

Then run `composer.phar update`.

### The Groovey File

On your project root folder. Create a file called `groovey`. Or this could be any project name like `awesome`. Then cut copy paste the code below.

```php
#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/database.php';

use Groovey\Migration\Migration;
use Groovey\Migration\Adapters\Mysql;
use Groovey\Migration\Adapters\Adapter;

$adapter     = new Adapter(new Mysql);
$application = new Migration($adapter);

$application->run()
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

Good job! Your ready to discover the painless way of doing migrations.

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

This command creates a table called `migrations`. Then creates a folder `./database/migrations` under your project root.

    $ groovey migration:init

## Create

This command will automatically create the yaml file.

    $ groovey migration:create Create A Test Table

The command will generate the formatted file like `001_create_a_test_table.yml`.

## The YML file

This is where you store all your sql scripts.

`NOTES: Tabs needs to be converted to spaces. This is the rule for YML files.`

For more information about YML files please visit: http://www.yaml.org/start.html

`NOTES: All sql script has to end with a semicolon (;)`

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

    $ groovey migration:status

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

    $ groovey migration:up


Sample output:

    Running migration file (001_create_a_test_table.yml).

## List

Shows all the migrated yml scripts.

    $ groovey migration:list


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

    $ groovey migration:down

Reverse a specific migration version.

    $ groovey migration:down 001

Sample output:

    Downgrading migration file (001_create_a_test_table.yml).


## Reset

Truncates all migrated records.

    $ groovey migration:reset

Sample output:

    All datas will be truncated, are you sure you want to proceed? (Y/N): Y
    All datas has been cleared.

## Drop

Drops the `migrations` table.

    $ groovey migration:drop

Sample output:

    Migration table will be drop, are you sure you want to proceed? (Y/N): Y
    Migrations table gone.


## About

Shows the library information details.

    $ groovey migration:about

## Like us.

Give a `star` to show your support and love for the project.

## Contribution

Fork `Groovey Migration` and send us some issues.





























