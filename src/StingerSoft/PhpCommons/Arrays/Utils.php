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

namespace StingerSoft\PhpCommons\Arrays;

use Closure;

/**
 * Provides helper methods to handle PHP native arrays
 */
abstract class Utils {

	/**
	 * Adds a given element into the array on the given position without replacing the old entry
	 *
	 * @param array $array
	 *            The array the element should be insert into
	 * @param mixed $element
	 *            The element to insert
	 * @param int   $position
	 *            The position to insert the element into the array.
	 *            If this value is greater than the array size, the element will be put at the end of the array
	 * @return array
	 */
	public static function insertElement(array $array, $element, int $position): array {
		$res = array_slice($array, 0, $position, true);

		if(is_array($element)) {
			$res = array_merge($res, $element);
		} else {
			$res[] = $element;
		}

		$res = array_merge($res, array_slice($array, $position, count($array) - 1, true));

		return $res;
	}

	/**
	 * Removes the given element from the array
	 *
	 * @param array $array
	 *            The array the element should be removed from
	 * @param mixed $value
	 *            The value to be removed
	 * @return array
	 */
	public static function removeElementByValue(array $array, $value): array {
		if(($key = array_search($value, $array, true)) !== false) {
			unset($array[$key]);
		}
		return $array;
	}

	/**
	 * Creates an array of arrays from the two given arrays.
	 * If one array is bigger than the other, missing values will be filled up with nulls.
	 *
	 * @param array $array1
	 * @param array $array2
	 * @return array[array]
	 */
	public static function mergeArrayValues(array $array1, array $array2): array {
		return array_map(null, $array1, $array2);
	}

	/**
	 * Returns the previous key from an array
	 *
	 * @param mixed $key
	 * @param array $array
	 * @return boolean|mixed previous key or false if no previous key is available
	 */
	public static function getPrevKey($key, array $array) {
		$keys = array_keys($array);
		$found_index = array_search($key, $keys, true);
		if($found_index === false || $found_index === 0) {
			return false;
		}
		return $keys[$found_index - 1];
	}

	/**
	 * Returns the next key from an array
	 *
	 * @param mixed $key
	 * @param array $array
	 * @return boolean|mixed next key or false if no next key is available
	 */
	public static function getNextKey($key, array $array) {
		$keys = array_keys($array);
		$found_index = array_search($key, $keys, true);
		if($found_index === false || $found_index + 1 === count($keys)) {
			return false;
		}
		return $keys[$found_index + 1];
	}

	/**
	 * Applies a callback on a part of a multidimensional array defined by its path (ie keys)
	 *
	 * @param array            $array
	 *            The array the callable should be applied on
	 * @param array            $path
	 *            An array of keys representing the path to the targeted element
	 * @param callable|Closure $callback
	 *            Callback to apply on the targeted element. The targeted subarray and the last key of the path will be passed to this delegate
	 * @return array|null Returns the modified array or null if no element could by found specified by the path
	 */
	public static function applyCallbackByPath(array &$array, array $path, $callback): ?array {
		$i = 0;
		while($i < count($path) - 1) {
			$piece = $path[$i];
			if(!is_array($array) || !array_key_exists($piece, $array)) {
				return null;
			}
			$array = &$array[$piece];
			$i++;
		}
		$piece = end($path);
		if(!is_array($array) || !array_key_exists($piece, $array)) {
			return null;
		}
		call_user_func_array($callback, [
			&$array,
			$piece,
		]);
		return $array;
	}
}