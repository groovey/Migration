<?php

namespace Groovey\Migration;

use Symfony\Component\Finder\Finder;

class Migration
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public static function getTemplate()
    {
        $now  = new \DateTime();
        $date = $now->format('Y-m-d H:i:s');

        $yaml = <<<YML
date: 'YYYY-mm-dd HH:mm:ss'
author: Groovey
changelog: >

up: >


down: >

YML;

        return $yaml;
    }

    public static function getDirectory()
    {
        return getcwd().'/database/migrations';
    }

    public static function getAllFiles()
    {
        $finder = new Finder();

        return $finder->files()->in(self::getDirectory());
    }

    public static function getFileInfo($file)
    {
        list($version, $extension) = explode('.', $file);

        return [
            'version'   => $version,
            'extension' => $extension,
        ];
    }

    public static function getUnMigratedFiles($app)
    {
        $records = function ($app) {
            $version    = [];
            $migrations = $app['db']->table('migrations')->orderBy('version')->get();
            foreach ($migrations as $file) {
                $version[] = $file->version;
            }

            return $version;
        };

        $files = [];
        foreach (self::getAllFiles() as $file) {
            $filename = $file->getRelativePathname();
            $version  = substr($filename, 0, -4);

            if (!in_array($version, $records($app))) {
                $files[] = $filename;
            }
        }

        return (array) $files;
    }
}
