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
        $now = new \DateTime();
        $date = $now->format('Y-m-d H:i:s');

        $yaml = <<<YML
# Author: Name
# Date: $date

# Run the migration
UP: >


# Reverse the migration
DOWN: >


YML;

        return $yaml;
    }

    public static function getDirectory()
    {
        return getcwd().'/database/migrations';
    }

    public static function getGeneratedVersion()
    {
        $finder = new Finder();
        $finder->files()->in(self::getDirectory());

        $versions = ['000'];

        foreach ($finder as $file) {
            $filename = $file->getRelativePathname();

            list($version, $description) = explode('_', $filename);
            $versions[] =  $version;
        }

        $last = end($versions);
        $new  = (int) $last + 1;

        return str_pad($new, 3, '0', STR_PAD_LEFT);
    }

    public static function getGeneratedFilename($argument)
    {
        $version = self::getGeneratedVersion();

        return $version.'_'.strtolower($argument).'.yml';
    }

    public static function getAllFiles()
    {
        $finder = new Finder();

        return $finder->files()->in(self::getDirectory());
    }

    public static function getFileInfo($file)
    {
        list($version) = explode('_', $file);

        $description = str_replace('_', ' ', substr($file, 4, -4));

        return [
            'version'     => $version,
            'description' => $description,
        ];
    }

    public static function getUnMigratedFiles($app)
    {
        $records = function ($app) {
            $version = [];
             $migrations = $app['db']->table('migrations')->orderBy('version')->get();
            foreach ($migrations as $file) {
                $version[] = $file->version;
            }

            return $version;
        };

        $files = [];
        foreach (self::getAllFiles() as $file) {
            $filename = $file->getRelativePathname();
            list($version, $description) = explode('_', $filename);
            if (!in_array($version, $records($app))) {
                $files[] = $filename;
            }
        }

        return (array) $files;
    }
}
