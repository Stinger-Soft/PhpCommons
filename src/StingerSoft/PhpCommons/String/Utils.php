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
			return true;
		}
		if($needle === null && $haystack !== null) {
			return true;
		}
		if($needle !== null && $haystack === null) {
			return false;
		}
		return $needle === '' || mb_strrpos($haystack, $needle, -strlen($haystack)) !== false;
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
		return $needle === '' || (($temp = mb_strlen($haystack) - mb_strlen($needle)) >= 0 && mb_strpos($haystack, $needle, $temp) !== false);
	}

	/**
	 * Uppercase the first character of each word in a string
	 *
	 * @param string $input
	 *            The string to be camelized
	 * @param string $separator
	 *            Each character after this string will be uppercased
	 * @param bool $capitalizeFirstCharacter
	 * @return string
	 */
	public static function camelize($input, $separator = '_', $capitalizeFirstCharacter = false): string {
		$result = str_replace($separator, '', ucwords($input, $separator));
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
	 * @param string $text
	 *            The text to extract the excerpt from
	 * @param string|array $phrase
	 *            The phrases to search for.
	 * @param int $radius
	 *            The radius in characters to be included in the excerpt
	 * @param string $ending
	 *            The string that should be appended after the excerpt
	 * @return string The created excerpt
	 */
	public static function excerpt(string $text, $phrase, int $radius = 100, string $ending = '...'): string {
		$phrases = is_array($phrase) ? $phrase : [
			$phrase,
		];

		$phraseLen = mb_strlen(implode(' ', $phrases));
		if($phraseLen !== false && $radius < $phraseLen) {
			$radius = $phraseLen;
		}

		$pos = 0;
		foreach($phrases as $tmpPhrase) {
			$pos = mb_stripos($text, $tmpPhrase);
			if($pos > -1) {
				break;
			}
		}

		$startPos = 0;
		if($pos > $radius) {
			$startPos = $pos - $radius;
		}

		$textLen = mb_strlen($text);

		$endPos = $pos + $phraseLen + $radius;
		if($endPos >= $textLen) {
			$endPos = $textLen;
		}

		$excerpt = mb_substr($text, $startPos, $endPos - $startPos);
		if($startPos !== 0) {
			$excerpt = self::mb_substr_replace($excerpt, $ending, 0, $phraseLen);
		}

		if($endPos !== $textLen) {
			$excerpt = self::mb_substr_replace($excerpt, $ending, -$phraseLen);
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
	 * @param int $start
	 *            The start position offset. Number of characters from the beginning of string. (First character is 0)
	 * @param int $max
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
	 * Generates initials of the given string value.
	 *
	 * Every "first" character [a-Z] after a "stop" (whitespace, dot, dash etc.) character
	 * is appended to the initials.
	 *
	 * Initials cannot be generated for numbers.
	 *
	 * @param string|null $value the string value to generate the initials for
	 * @param bool $toUppercase
	 *                                whether the characters of the initials shall be changed to upper case.
	 * @return string|null the initials of the given value or null in case the given value was null.
	 */
	public static function initialize(?string $value, bool $toUppercase = true): ?string {
		if($value === null) {
			return null;
		}
		$expr = '/(?<=\b)[a-z]/i';
		preg_match_all($expr, $value, $matches);
		$letters = $matches[0];
		if(count($letters)) {
			$result = implode('', $matches[0]);
			return $toUppercase ? strtoupper($result) : $result;
		}
		return $value;
	}

	/**
	 * Get an integer based hash code of the given string.
	 *
	 * @param mixed $string the string to be hashed
	 * @return int the hash code of the given string
	 */
	public static function hashCode($string): int {
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

	/**
	 *
	 * https://gist.github.com/JBlond/942f17f629f22e810fe3
	 *
	 * @param string|string[] $string The input string.
	 * @param string|string[] $replacement The replacement string.
	 * @param int|int[] $start If start is positive, the replacing will begin at the start'th offset into string.  If start is negative, the replacing will begin at the start'th character from the end of string.
	 * @param int|int[]|null $length If given and is positive, it represents the length of the portion of string which is to be replaced. If it is negative, it represents the number of characters from the end of string at which to stop replacing. If it is not given, then it will default to strlen( string ); i.e. end the replacing at the end of string. Of course, if length is zero then this function will have the effect of inserting replacement into string at the given start offset.
	 * @return string|string[] The result string is returned. If string is an array then array is returned.
	 */
	public static function mb_substr_replace($string, $replacement, $start, ?int $length = null) {
		if(is_array($string)) {
			$num = count($string);
			// $replacement
			$replacement = is_array($replacement) ? array_slice($replacement, 0, $num) : array_pad(array($replacement), $num, $replacement);
			// $start
			if(is_array($start)) {
				$start = array_slice($start, 0, $num);
				foreach($start as $key => $value) {
					$start[$key] = is_int($value) ? $value : 0;
				}
			} else {
				$start = array_pad(array($start), $num, $start);
			}
			// $length
			if(!isset($length)) {
				$length = array_fill(0, $num, 0);
			} elseif(is_array($length)) {
				$length = array_slice($length, 0, $num);
				foreach($length as $key => $value) {
					$length[$key] = isset($value) ? (is_int($value) ? $value : $num) : 0;
				}
			} else {
				$length = array_pad(array($length), $num, $length);
			}
			// Recursive call
			return array_map(__FUNCTION__, $string, $replacement, $start, $length);
		}
		preg_match_all('/./us', (string)$string, $smatches);
		preg_match_all('/./us', (string)$replacement, $rmatches);
		if($length === null) {
			$length = mb_strlen($string);
		}
		array_splice($smatches[0], $start, $length, $rmatches[0]);
		return implode($smatches[0]);
	}

	private static function overflow32($v): int {
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
