<?php namespace Groovey\Migration;

use Symfony\Component\Finder\Finder;
use Groovey\Migration\Models\Migration as Migrations;

class Manager
{

    public function __construct()
    {
    }

    public static function getTemplate()
    {

$yaml = <<<YML
# Author: Name <your@email.com>

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

        return str_pad($new, 3, "0", STR_PAD_LEFT);
    }

    public static function getGeneratedFilename($argument)
    {
        $version = self::getGeneratedVersion();

        return $version . '_' . strtolower($argument) . '.yml';
    }

    public static function getAllFiles()
    {
        $finder = new Finder();

        return $finder->files()->in(self::getDirectory());
    }

    public static function getUnMigratedFiles()
    {

        $records = function () {
            $version = [];
            $migrations = Migrations::orderBy('version')->get();
            foreach ($migrations as $file) {
                $version[] = $file->version;
            }

            return $version;
        };

        $files = [];
        foreach (self::getAllFiles() as $file) {
            $filename = $file->getRelativePathname();
            list($version, $description) = explode('_', $filename);

            if (!in_array($version, $records())) {
                $files[] = $filename;
            }
        }

        return (array) $files;
    }

    public static function getFileInfo($file)
    {

        list($version) = explode('_', $file);

        $description = str_replace('_', ' ', substr($file, 4, -4));

        return [
            'version'     => $version,
            'description' => $description
        ];
    }
}
