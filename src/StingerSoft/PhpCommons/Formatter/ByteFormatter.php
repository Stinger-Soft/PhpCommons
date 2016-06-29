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
namespace StingerSoft\PhpCommons\Formatter;

/**
 * Provides methods to handle memory sizes
 */
abstract class ByteFormatter {

	/**
	 * Pretty prints a given memory size
	 *
	 * @param integer $size
	 *        	The memory size in bytes
	 * @param boolean $si
	 *        	Use SI prefixes?
	 * @param string $locale
	 *        	Locale used to format the result
	 * @return string A pretty printed memory size
	 */
	public static function prettyPrintSize($size, $precision = 2, $si = false, $locale = 'en') {
		$fmt = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
		
		$mod = $si ? 1000 : 1024;
		$base = log($size, $mod);
		$suffixes = null;
		if($si) {
			$suffixes = [
				'B',
				'kB',
				'MB',
				'GB',
				'TB',
				'PB' 
			];
		} else {
			$suffixes = [
				'B',
				'kiB',
				'MiB',
				'GiB',
				'TiB',
				'PiB' 
			];
		}
		
		return $fmt->format(round(pow($mod, $base - floor($base)), $precision)) . ' ' . $suffixes[floor($base)];
	}
}