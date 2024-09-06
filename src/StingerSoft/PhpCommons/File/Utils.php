<?php

/*
 * This file is part of the Stinger PHP-Commons package.
 *
 * (c) Oliver Kotte <oliver.kotte@stinger-soft.net>
 * (c) Florian Meyer <florian.meyer@stinger-soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace StingerSoft\PhpCommons\File;

/**
 * Encapsulates common methods related to file handling
 */
class Utils {


    /**
     * Creates a temporary file with the given content
     *
     * @param  string $extension
     * @param  string $prefix
     * @param  mixed  $content
     * @return string the filename of the temporary file
     */
    public static function createTemporaryFile($extension = null, $prefix = 'pec_platform', $content = null) {
        $filename = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . uniqid($prefix, true);
        if(null !== $extension) {
            $filename .= '.' . $extension;
        }
        if(null !== $content) {
            file_put_contents($filename, $content);
        }
        return $filename;
    }

    /**
     * Sanitize the given filename
     *
     * @param  string $filename
     * @return string
     */
    public static function escapeFilename($filename) {
        return preg_replace('/[^a-zA-Z0-9- !@#$%^()]/', '_', $filename);
    }

    /**
     * Writes the given content into the specified file
     *
     * @param  string $path
     * @param  string $contents
     * @throws \RuntimeException
     */
    public static function write($path, $contents) {
        if(!is_dir($dir = dirname($path)) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException('Unable to create directory ' . $dir);
        }

        if(false === @file_put_contents($path, $contents)) {
            throw new \RuntimeException('Unable to write file ' . $path);
        }
    }
}
