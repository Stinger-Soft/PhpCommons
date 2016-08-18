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
namespace StingerSoft\PhpCommons\Integer;

/**
 * Provides some 'missing' methods to handle integers
 */
class Utils {

	/**
	 * Returns an integer less than, equal to, or greater than zero if the first argument is considered to be
	 * respectively less than, equal to, or greater than the second.
	 *
	 * @param int $a        	
	 * @param int $b        	
	 * @return int
	 */
	public static function intcmp($a, $b) {
		return ($a - $b) ? ($a - $b) / abs($a - $b) : 0;
	}
}