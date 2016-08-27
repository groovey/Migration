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

    public static function validateFileFormat($file)
    {
        list($basename, $extension) = explode('.', $file);
        $version     = substr($basename, 0, 3);
        $description = substr($basename, 4);

        if (preg_match('/[0-9][0-9][0-9]_/', $file, $output)) {
            return true;
        }

        return false;
    }

    public static function getFileInfo($file)
    {
        list($basename, $extension) = explode('.', $file);
        $version     = substr($basename, 0, 3);
        $description = substr($basename, 4);

        return [
            'version'     => $version,
            'description' => $description,
            'extension'   => $extension,
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
            $files[] = $data->filename;
        }

        return $files;
    }

    public static function getNextVersion($app)
    {
        $migratedFiles   = self::getMigratedFiles($app);
        $unMigratedFiles = self::getUnMigratedFiles($app);

        if (!$unMigratedFiles) {
            $last = end($migratedFiles);
        } else {
            $last = end($unMigratedFiles);
        }

        $lastVersion = substr($last, 0, 3);
        $lastVersion += 1;
        $nextVersion     = str_pad($lastVersion, 3, '0', STR_PAD_LEFT);

        return $nextVersion;
    }
}
