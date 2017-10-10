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
        list($major, $minor, $build) = explode('.', $file);

        return [
            'major' => $major,
            'minor' => $minor,
            'build' => $build,
        ];
    }

    public static function getUnMigratedFiles($app)
    {
        $migratedFiles = self::getMigratedFiles($app);
        $files         = [];

        foreach (self::getAllFiles() as $file) {
            $filename = $file->getRelativePathname();
            if (!in_array($filename, $migratedFiles)) {
                $files[] = $filename;
            }
        }

        return (array) $files;
    }

    public static function getMigratedFiles($app)
    {
        $files = [];
        $datas = $app['db']->table('migrations')->orderBy('version')->get();
        foreach ($datas as $data) {
            $files[] = $data->version.'.yml';
        }

        return $files;
    }

    public static function validateVersion($version)
    {
        $total = substr_count($version, '.');

        if ($total == 2) {
            return true;
        }

        return false;
    }
}
