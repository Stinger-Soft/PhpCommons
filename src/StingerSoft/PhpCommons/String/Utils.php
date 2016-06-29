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
}
