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

	const UNIT_SECONDS = 1;

	const UNIT_MINUTES = 2;

	const UNIT_HOURS = 3;

	const UNIT_DAYS = 4;

	const UNIT_WEEKS = 5;

	const UNIT_MONTHS = 6;

	const UNIT_YEARS = 7;

	const MINUTE_IN_SECONDS = 60;

	const HOUR_IN_SECONDS = 3600;

	const DAY_IN_SECONDS = 86400;

	const WEEK_IN_SECONDS = 604800;

	const YEAR_IN_SECONDS = 31536000;

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
		$seconds = abs($endTime - $startTime);
		return sprintf('%02d:%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60), $seconds % 60);
	}

	/**
	 * Get the relative difference between a given start time and end time.
	 *
	 * Depending on the amount of time passed between given from and to, the difference between the two may be
	 * expressed in seconds, hours, days, weeks, months or years.
	 *
	 * @param int|\DateTime $from
	 *        	the start time, either as <code>DateTime</code> object or as integer expressing a unix timestamp,
	 *        	used for calculating the relative difference to the given <code>to</code> parameter.
	 * @param int|\DateTime|null $to
	 *        	the end time, either as <code>DateTime</code> object, a unix timestamp or <code>null</code> used for
	 *        	calculating the difference to the given <code>from</code> parameter. In case <code>null</code> is
	 *        	provided, the current timestamp will be used (utilizing <code>time()</code>).
	 * @return int[2] returns an array containing two values: first, the relative difference
	 *         between <code>from</code> and </code>to</code>, second a constant for the time unit of the relative
	 *         difference. The unit is one of the class constants: <code>UNIT_SECONDS</code>, <code>UNIT_MINUTES</code>,
	 *         <code>UNIT_HOURS</code>, <code>UNIT_DAYS</code>, <code>UNIT_WEEKS</code>, <code>UNIT_MONTHS</code> or
	 *         <code>UNIT_YEARS</code>
	 * @throws \InvalidArgumentException in case <code>from</code> is neither an integer, nor
	 *         a <code>DateTime</code> object.
	 * @throws \InvalidArgumentException in case <code>to</code> is neither an integer,
	 *         nor a <code>DateTime</code> object, nor <code>null</code>.
	 *        
	 */
	public static function getRelativeTimeDifference($from, $to = null) {
		if($from instanceof \DateTime) {
			$from = $from->getTimestamp();
		} else if(!is_int($from)) {
			throw new \InvalidArgumentException('$from must be a \DateTime or an integer!');
		}
		
		if($to == null) {
			$to = time();
		} else if($to instanceof \DateTime) {
			$to = $to->getTimestamp();
		} else if(!is_int($to)) {
			throw new \InvalidArgumentException('$to must be a \DateTime, null or an integer!');
		}
		
		$diff = (int)abs($to - $from);
		
		$since = $diff;
		$unit = self::UNIT_SECONDS;
		if($diff < self::MINUTE_IN_SECONDS) {
			// leave empty here, because this is the default case
		} else if($diff < self::HOUR_IN_SECONDS) {
			$since = max(1, round($diff / self::MINUTE_IN_SECONDS));
			$unit = self::UNIT_MINUTES;
		} elseif($diff < self::DAY_IN_SECONDS && $diff >= self::HOUR_IN_SECONDS) {
			$since = max(1, round($diff / self::HOUR_IN_SECONDS));
			$unit = self::UNIT_HOURS;
		} elseif($diff < self::WEEK_IN_SECONDS && $diff >= self::DAY_IN_SECONDS) {
			$since = max(1, round($diff / self::DAY_IN_SECONDS));
			$unit = self::UNIT_DAYS;
		} elseif($diff < 30 * self::DAY_IN_SECONDS && $diff >= self::WEEK_IN_SECONDS) {
			$since = max(1, round($diff / self::WEEK_IN_SECONDS));
			$unit = self::UNIT_WEEKS;
		} elseif($diff < self::YEAR_IN_SECONDS && $diff >= 30 * self::DAY_IN_SECONDS) {
			$since = max(1, round($diff / (30 * self::DAY_IN_SECONDS)));
			$unit = self::UNIT_MONTHS;
		} elseif($diff >= self::YEAR_IN_SECONDS) {
			$since = max(1, round($diff / self::YEAR_IN_SECONDS));
			$unit = self::UNIT_YEARS;
		}
		
		return array(
			$since,
			$unit 
		);
	}
}