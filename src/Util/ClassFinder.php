<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Util;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

use function array_filter;
use function basename;
use function end;
use function get_declared_classes;
use function in_array;
use function is_file;
use function strrpos;
use function substr;

final class ClassFinder
{
    public function findInDir(array $directories, string $filePattern): array
    {
        $classNames = [];
        foreach ($directories as $directory) {
            $recursiveDirectoryIterator = new RecursiveDirectoryIterator($directory);
            $recursiveIteratorIterator  = new RecursiveIteratorIterator($recursiveDirectoryIterator);
            $regexIterator              = new RegexIterator($recursiveIteratorIterator, $filePattern, RegexIterator::GET_MATCH);


            foreach ($regexIterator as $file) {
                $filePath = $file[0] ?? false;
                if ($filePath && is_file($filePath)) {
                    require_once $filePath;
                    $fileExtension = '.' . end($file);
                    $classNames[]  = basename($filePath, $fileExtension);
                }
            }
        }

        return array_filter(
            get_declared_classes(),
            fn($declared) => in_array($this->removeNamespace($declared), $classNames)
        );
    }

    private function removeNamespace(string $str): string
    {
        $offset = strrpos($str, '\\');
        if ($offset > 0) {
            return substr($str, $offset + 1);
        }
        return $str;
    }
}
