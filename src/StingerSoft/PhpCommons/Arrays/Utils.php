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
namespace StingerSoft\PhpCommons\Arrays;

abstract class Utils {

	/**
	 * Adds a given element into the array on the given position without replacing the old entry
	 *
	 * @param array $array
	 *        	The array the element should be insert into
	 * @param mixed $element
	 *        	The element to insert
	 * @param integer $position
	 *        	The position to insert the element into the array.
	 *        	If this value is greater than the array size, the element will be put at the end of the array
	 * @return array
	 */
	public static function insertElement(array $array, $element, $position) {
		$res = array_slice($array, 0, $position, true);
		
		if(is_array($element)) {
			$res = array_merge($res, $element);
		} else {
			array_push($res, $element);
		}
		
		$res = array_merge($res, array_slice($array, $position, count($array) - 1, true));
		
		return $res;
	}

	/**
	 * Removes the given element from the array
	 *
	 * @param array $array
	 *        	The array the element should be removed from
	 * @param mixed $value
	 *        	The value to be removed
	 * @return array
	 */
	public static function removeElementByValue(array $array, $value) {
		if(($key = array_search($value, $array)) !== false) {
			unset($array[$key]);
		}
		return $array;
	}
}