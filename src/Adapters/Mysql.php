<?php namespace Groovey\Migration\Adapters;

use Illuminate\Database\Capsule\Manager as DB;

class Mysql implements AdapterInterface
{
    public static function create()
    {

        $sql = '
            CREATE TABLE IF NOT EXISTS `migrations` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `version` char(3) COLLATE utf8_unicode_ci NOT NULL,
              `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `created_at` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
        ';

        return DB::statement($sql);
    }

    public static function drop()
    {
        $sql = 'DROP TABLE IF EXISTS `migrations`;';

        return DB::statement($sql);
    }

    public static function truncate()
    {
        $sql = 'TRUNCATE TABLE `migrations`';

        return DB::statement($sql);
    }

}
