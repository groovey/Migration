<?php namespace Groovey\Migration\Adapters;

interface AdapterInterface
{
    public static function create();
    public static function drop();
    public static function reset();
}
