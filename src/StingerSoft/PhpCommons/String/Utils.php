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
namespace StingerSoft\PhpCommons\String;

/**
 * Provides some 'missing' methods to handle strings
 */
abstract class Utils {

	/**
	 * Checks if the haystack starts with needle
	 *
	 * @param string $haystack        	
	 * @param string $needle        	
	 * @return boolean
	 */
	public static function startsWith($haystack, $needle) {
		if($needle === null && $haystack === null)
			return false;
		if($needle === null && $haystack !== null)
			return true;
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}

	/**
	 * Checks if the haystack ends with needle
	 *
	 * @param string $haystack        	
	 * @param string $needle        	
	 * @return boolean
	 */
	public static function endsWith($haystack, $needle) {
		if($needle === null && $haystack === null)
			return true;
		if($needle === null && $haystack !== null)
			return true;
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}

	/**
	 * Uppercase the first character of each word in a string
	 *
	 * @param string $input
	 *        	The string to be camelized
	 * @param string $separator
	 *        	Each character after this string will be uppercased
	 * @return string
	 */
	public static function camelize($input, $separator = '_', $capitalizeFirstCharacter = false) {
		if(version_compare(PHP_VERSION, '5.5.16') >= 0 && !defined('HHVM_VERSION')) {
			return self::camelizeNewVersion($input, $separator, $capitalizeFirstCharacter);
		}
		return self::camelizeOldVersion($input, $separator, $capitalizeFirstCharacter);
	}

	/**
	 * Uppercase the first character of each word in a string (PHP_VERSION bigger or equal than 5.5.16)
	 *
	 * @param string $input
	 *        	The string to be camelized
	 * @param string $separator
	 *        	Each character after this string will be uppercased
	 * @return string
	 */
	protected static function camelizeNewVersion($input, $separator = '_', $capitalizeFirstCharacter = false) {
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
	 *        	The string to be camelized
	 * @param string $separator
	 *        	Each character after this string will be uppercased
	 * @return string
	 */
	protected static function camelizeOldVersion($input, $separator = '_', $capitalizeFirstCharacter = false) {
		$result = implode('', array_map(function ($key) {
			return ucfirst($key);
		}, explode($separator, $input)));
		
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
	 *        	The text to extract the excerpt from
	 * @param string|array $phrase
	 *        	The phrases to search for.
	 * @param number $radius
	 *        	The radius in characters to be included in the excerpt
	 * @param string $ending
	 *        	The string that should be appended after the excerpt
	 * @return string The created excerpt
	 */
	public static function excerpt($text, $phrase, $radius = 100, $ending = "...") {
		$phrases = is_array($phrase) ? $phrase : array(
			$phrase 
		);
		
		$phraseLen = strlen(implode(' ', $phrases));
		if($radius < $phraseLen) {
			$radius = $phraseLen;
		}
		
		foreach($phrases as $phrase) {
			$pos = strpos(strtolower($text), strtolower($phrase));
			if($pos > -1)
				break;
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
		if($startPos != 0) {
			$excerpt = substr_replace($excerpt, $ending, 0, $phraseLen);
		}
		
		if($endPos != $textLen) {
			$excerpt = substr_replace($excerpt, $ending, -$phraseLen);
		}
		
		return $excerpt;
	}

	/**
	 * Highlights a given keyword in a string
	 *
	 * @param string $string
	 *        	The string to search in
	 * @param string $keyword
	 *        	The keyword to be highlighted
	 * @param string $preHighlight
	 *        	This string will be attached before every match
	 * @param string $postHightlight
	 *        	This string will be attached after every match
	 * @return string
	 */
	public static function highlight($string, $keyword, $preHighlight = '<em>', $postHightlight = '</em>') {
		return preg_replace("/\p{L}*?" . preg_quote($keyword) . "\p{L}*/ui", $preHighlight . "$0" . $postHightlight, $string);
	}

	/**
	 *
	 * Get truncated string with specified width.
	 * 
	 * @param string $value
	 *        	The string being truncated
	 * @param number $start
	 *        	The start position offset. Number of characters from the beginning of string. (First character is 0)
	 * @param number $max
	 *        	The width of the desired trim
	 * @param string $truncationSymbol
	 *        	A string that is added to the end of string when string is truncated
	 * @return string
	 */
	public static function truncate($value, $start = 0, $max = 31, $truncationSymbol = '...') {
		$valueEncoding = mb_detect_encoding($value, 'auto', true);
		return mb_strimwidth($value, $start, $max, $truncationSymbol, $valueEncoding);
	}

	/**
	 * Get an integer based hash code of the given string.
	 *
	 * @param string $string the string to be hashed
	 * @return int the hash code of the given string
	 */
	public static function hashCode($string) {
		// Code from https://stackoverflow.com/a/40688976/3918483
		$hash = 0;
		if(!is_string($string)) {
			return $hash;
		}

		$len = mb_strlen($string, 'UTF-8');
		if($len === 0) {
			return $hash;
		}
		for($i = 0; $i < $len; $i++) {
			$c = mb_substr($string, $i, 1, 'UTF-8');
			$cc = unpack('V', iconv('UTF-8', 'UCS-4LE', $c))[1];
			$hash = (($hash << 5) - $hash) + $cc;
			$hash &= $hash; // 16bit > 32bit
		}
		return $hash;
	}
}
