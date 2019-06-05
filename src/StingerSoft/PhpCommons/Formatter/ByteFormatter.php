<?php
declare(strict_types=1);

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

use NumberFormatter;

/**
 * Provides methods to handle memory sizes
 */
abstract class ByteFormatter {

	/**
	 * Pretty prints a given memory size
	 *
	 * @param int         $size
	 *            The memory size in bytes
	 * @param int         $precision
	 * @param bool        $si
	 *            Use SI prefixes?
	 * @param string|null $locale
	 *            Locale used to format the result
	 * @return string A pretty printed memory size
	 */
	public static function prettyPrintSize(int $size, int $precision = 2, bool $si = false, ?string $locale = 'en'): string {
		$fmt = new NumberFormatter($locale, NumberFormatter::DECIMAL);

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
				'PB',
			];
		} else {
			$suffixes = [
				'B',
				'kiB',
				'MiB',
				'GiB',
				'TiB',
				'PiB',
			];
		}

		return $fmt->format(round($mod ** ($base - floor($base)), $precision)) . ' ' . $suffixes[(int)floor($base)];
	}
}