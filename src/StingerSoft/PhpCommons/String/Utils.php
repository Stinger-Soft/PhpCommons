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

namespace StingerSoft\PhpCommons\String;

/**
 * Provides some 'missing' methods to handle strings
 */
abstract class Utils {

	/**
	 * Checks if the haystack starts with needle
	 *
	 * @param string|null $haystack
	 * @param string|null $needle
	 * @return boolean
	 */
	public static function startsWith(?string $haystack, ?string $needle): bool {
		if($needle === null && $haystack === null) {
			return false;
		}
		if($needle === null && $haystack !== null) {
			return true;
		}
		if($needle !== null && $haystack === null) {
			return false;
		}
		return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}

	/**
	 * Checks if the haystack ends with needle
	 *
	 * @param string|null $haystack
	 * @param string|null $needle
	 * @return boolean
	 */
	public static function endsWith(?string $haystack, ?string $needle): bool {
		if($needle === null && $haystack === null) {
			return true;
		}
		if($needle === null && $haystack !== null) {
			return true;
		}
		if($needle !== null && $haystack === null) {
			return false;
		}
		return $needle === '' || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}

	/**
	 * Uppercase the first character of each word in a string
	 *
	 * @param string $input
	 *            The string to be camelized
	 * @param string $separator
	 *            Each character after this string will be uppercased
	 * @param bool   $capitalizeFirstCharacter
	 * @return string
	 */
	public static function camelize($input, $separator = '_', $capitalizeFirstCharacter = false): string {
		if(!defined('HHVM_VERSION') && version_compare(PHP_VERSION, '5.5.16') >= 0) {
			return self::camelizeNewVersion($input, $separator, $capitalizeFirstCharacter);
		}
		return self::camelizeOldVersion($input, $separator, $capitalizeFirstCharacter);
	}

	/**
	 * Uppercase the first character of each word in a string (PHP_VERSION bigger or equal than 5.5.16)
	 *
	 * @param string $input
	 *            The string to be camelized
	 * @param string $separator
	 *            Each character after this string will be uppercased
	 * @param bool   $capitalizeFirstCharacter
	 * @return string
	 */
	protected static function camelizeNewVersion($input, $separator = '_', $capitalizeFirstCharacter = false): string {
		$result = str_replace($separator, '', ucwords($input, $separator));
		if(!$capitalizeFirstCharacter) {
			$result = lcfirst($result);
		}
		return $result;
	}

	/**
	 * Uppercase the first character of each word in a string (PHP_VERSION less than 5.5.16)
	 *
	 * @param string $input
	 *            The string to be camelized
	 * @param string $separator
	 *            Each character after this string will be uppercased
	 * @param bool   $capitalizeFirstCharacter
	 * @return string
	 */
	protected static function camelizeOldVersion($input, $separator = '_', $capitalizeFirstCharacter = false): string {
		$result = implode('', array_map('ucfirst', explode($separator, $input)));

		if(!$capitalizeFirstCharacter) {
			$result = lcfirst($result);
		}
		return $result;
	}

	/**
	 * Creates an excerpt from the given text based on the passed phrase
	 *
	 * @see http://stackoverflow.com/a/1404151
	 *
	 * @param string       $text
	 *            The text to extract the excerpt from
	 * @param string|array $phrase
	 *            The phrases to search for.
	 * @param int          $radius
	 *            The radius in characters to be included in the excerpt
	 * @param string       $ending
	 *            The string that should be appended after the excerpt
	 * @return string The created excerpt
	 */
	public static function excerpt(string $text, $phrase, int $radius = 100, string $ending = '...'): string {
		$phrases = is_array($phrase) ? $phrase : [
			$phrase,
		];

		$phraseLen = strlen(implode(' ', $phrases));
		if($radius < $phraseLen) {
			$radius = $phraseLen;
		}

		$pos = 0;
		foreach($phrases as $tmpPhrase) {
			$pos = stripos($text, $tmpPhrase);
			if($pos > -1) {
				break;
			}
		}

		$startPos = 0;
		if($pos > $radius) {
			$startPos = $pos - $radius;
		}

		$textLen = strlen($text);

		$endPos = $pos + $phraseLen + $radius;
		if($endPos >= $textLen) {
			$endPos = $textLen;
		}

		$excerpt = substr($text, $startPos, $endPos - $startPos);
		if($startPos !== 0) {
			$excerpt = substr_replace($excerpt, $ending, 0, $phraseLen);
		}

		if($endPos !== $textLen) {
			$excerpt = substr_replace($excerpt, $ending, -$phraseLen);
		}

		return $excerpt;
	}

	/**
	 * Highlights a given keyword in a string
	 *
	 * @param string $string
	 *            The string to search in
	 * @param string $keyword
	 *            The keyword to be highlighted
	 * @param string $preHighlight
	 *            This string will be attached before every match
	 * @param string $postHighlight
	 *            This string will be attached after every match
	 * @return string
	 */
	public static function highlight(string $string, string $keyword, string $preHighlight = '<em>', string $postHighlight = '</em>'): string {
		return preg_replace("/\p{L}*?" . preg_quote($keyword, '/') . "\p{L}*/ui", $preHighlight . '$0' . $postHighlight, $string);
	}

	/**
	 *
	 * Get truncated string with specified width.
	 *
	 * @param string|null $value
	 *            The string being truncated
	 * @param int    $start
	 *            The start position offset. Number of characters from the beginning of string. (First character is 0)
	 * @param int    $max
	 *            The width of the desired trim
	 * @param string $truncationSymbol
	 *            A string that is added to the end of string when string is truncated
	 * @return string
	 */
	public static function truncate(?string $value, int $start = 0, int $max = 31, $truncationSymbol = '...'): ?string {
		if($value === null) {
			return null;
		}
		$valueEncoding = mb_detect_encoding($value, 'auto', true);
		return mb_strimwidth($value, $start, $max, $truncationSymbol, $valueEncoding);
	}

	/**
	 * Get an integer based hash code of the given string.
	 *
	 * @param mixed $string the string to be hashed
	 * @return int the hash code of the given string
	 */
	public
	static function hashCode($string): int {
		$h = 0;
		if(!is_string($string)) {
			return $h;
		}
		$len = strlen($string);
		for($i = 0; $i < $len; $i++) {
			$h = self::overflow32(31 * $h + ord($string[$i]));
		}
		return $h;
	}

	private
	static function overflow32($v): int {
		$v %= 4294967296;
		if($v > 2147483647) {
			return $v - 4294967296;
		}
		if($v < -2147483648) {
			return $v + 4294967296;
		}
		return $v;
	}
}
