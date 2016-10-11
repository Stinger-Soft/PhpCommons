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

class UtilsTest extends \PHPUnit_Framework_TestCase {

	public function testInsertElement() {
		$array = array(
			1,
			2,
			4,
			5 
		);
		$expectedArray = array(
			1,
			2,
			3,
			4,
			5 
		);
		$result = Utils::insertElement($array, 3, 2);
		$this->assertEquals($expectedArray, $result);
	}

	public function testInsertElementWrongPosition() {
		$array = array(
			1,
			2,
			4,
			5 
		);
		$expectedArray = array(
			1,
			2,
			4,
			5,
			3 
		);
		$result = Utils::insertElement($array, 3, 8);
		$this->assertEquals($expectedArray, $result);
	}

	public function testInsertElementAssoc() {
		$array = array(
			"one" => 1,
			"two" => 2,
			"four" => 4,
			"five" => 5 
		);
		$expectedArray = array(
			"one" => 1,
			"two" => 2,
			"three" => 3,
			"four" => 4,
			"five" => 5 
		);
		$result = Utils::insertElement($array, array(
			"three" => 3 
		), 2);
		$this->assertEquals($expectedArray, $result);
	}

	public function testRemoveByValue() {
		$array = array(
			'test1',
			'test2',
			'test3',
			'test4' 
		);
		$result = Utils::removeElementByValue($array, 'test2');
		$this->assertCount(count($array) - 1, $result);
		$this->assertNotContains('test2', $result);
		
		$result = Utils::removeElementByValue($array, 'test99999999');
		$this->assertCount(count($array), $result);
	}

	public function testMergeArrayValues() {
		$array1 = array(
			'a',
			'b',
			'c' 
		);
		$array2 = array(
			1,
			2,
			3 
		);
		
		$expected = array(
			array(
				'a',
				1 
			),
			array(
				'b',
				2 
			),
			array(
				'c',
				3 
			) 
		);
		
		$result = Utils::mergeArrayValues($array1, $array2);
		
		$this->assertEquals($expected, $result);
	}

	public function testMergeArrayValuesWithDifferentSize() {
		$array1 = array(
			'a',
			'b' 
		);
		$array2 = array(
			1,
			2,
			3 
		);
		
		$expected = array(
			array(
				'a',
				1 
			),
			array(
				'b',
				2 
			),
			array(
				null,
				3 
			) 
		);
		
		$result = Utils::mergeArrayValues($array1, $array2);
		
		$this->assertEquals($expected, $result);
	}

	public function testGetPrevKey() {
		$testArray = array(
			'a' => 'A',
			'b' => 'B' 
		);
		$this->assertEquals('a', Utils::getPrevKey('b', $testArray));
		$this->assertEquals(false, Utils::getPrevKey('a', $testArray));
		$this->assertEquals(false, Utils::getPrevKey('c', $testArray));
	}

	public function testGetNextKey() {
		$testArray = array(
			'a' => 'A',
			'b' => 'B' 
		);
		$this->assertEquals('b', Utils::getNextKey('a', $testArray));
		$this->assertEquals(false, Utils::getNextKey('b', $testArray));
		$this->assertEquals(false, Utils::getNextKey('c', $testArray));
	}

	public function testApplyCallbackByPath() {
		$testArray = array(
			'a' => 'A',
			'b' => 'B',
			'children' => array(
				'test1',
				'test2',
				'test3' 
			),
			'siblings' => array(
				'left' => 'sister',
				'right' => 'brother' 
			) 
		);
		
		$unsetDelegate = function (&$array, $key) {
			unset($array[$key]);
		};
		
		$uppercaseDelegate = function (&$array, $key) {
			$array[$key] = strtoupper($array[$key]);
		};
		
		// remove item
		Utils::applyCallbackByPath($testArray, array(
			'a' 
		), $unsetDelegate);
		$this->assertArrayNotHasKey('a', $testArray);
		
		// set uppercase on value
		Utils::applyCallbackByPath($testArray, array(
			'siblings',
			'left' 
		), $uppercaseDelegate);
		$this->assertEquals($testArray['siblings']['left'], 'SISTER');
		
		// Non existing value
		$result = Utils::applyCallbackByPath($testArray, array(
			'siblings',
			'lefty' 
		), $uppercaseDelegate);
		$this->assertNull($result);
		
		// Non existing value
		$result = Utils::applyCallbackByPath($testArray, array(
			'siblings',
			'left',
			'test' 
		), $uppercaseDelegate);
		$this->assertNull($result);
		
		// Non existing value
		$result = Utils::applyCallbackByPath($testArray, array(
			'siblings',
			'left',
			'test',
			'test',
		), $uppercaseDelegate);
		$this->assertNull($result);
	}
}
