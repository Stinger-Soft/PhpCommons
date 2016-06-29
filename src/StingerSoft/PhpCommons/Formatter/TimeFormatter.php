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
 * Provides methods print timestamps and intervals
 */
abstract class TimeFormatter {

	/**
	 * Pretty prints an interval specfied by two timestamps
	 * 
	 * @param float $startTime
	 *        	Starttime as returned by microtime(true)
	 * @param float $endTime
	 *        	Endtime as returned by microtime(true)
	 * @param string $locale
	 *        	Locale used to format the result
	 */
	public static function prettyPrintMicroTimeInterval($startTime, $endTime) {
		$seconds = $endTime - $startTime;
		return sprintf('%02d:%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60), $seconds % 60);
	}
}