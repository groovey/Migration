<?php namespace Groovey\Migration\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Groovey\Migration\Adapters\Mysql;

class Migration extends Eloquent
{

    public $timestamps = false;

    public static function init()
    {
        // TODO: auto detect apapter.
        return Mysql::create();
    }

}
