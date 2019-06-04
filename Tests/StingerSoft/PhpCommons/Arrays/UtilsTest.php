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

use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase {

	public function testInsertElement(): void {
		$array = [
			1,
			2,
			4,
			5,
		];
		$expectedArray = [
			1,
			2,
			3,
			4,
			5,
		];
		$result = Utils::insertElement($array, 3, 2);
		$this->assertEquals($expectedArray, $result);
	}

	public function testInsertElementWrongPosition(): void {
		$array = [
			1,
			2,
			4,
			5,
		];
		$expectedArray = [
			1,
			2,
			4,
			5,
			3,
		];
		$result = Utils::insertElement($array, 3, 8);
		$this->assertEquals($expectedArray, $result);
	}

	public function testInsertElementAssoc(): void {
		$array = [
			'one'  => 1,
			'two'  => 2,
			'four' => 4,
			'five' => 5,
		];
		$expectedArray = [
			'one'   => 1,
			'two'   => 2,
			'three' => 3,
			'four'  => 4,
			'five'  => 5,
		];
		$result = Utils::insertElement($array, [
			'three' => 3,
		], 2);
		$this->assertEquals($expectedArray, $result);
	}

	public function testRemoveByValue(): void {
		$array = [
			'test1',
			'test2',
			'test3',
			'test4',
		];
		$result = Utils::removeElementByValue($array, 'test2');
		$this->assertCount(count($array) - 1, $result);
		$this->assertNotContains('test2', $result);

		$result = Utils::removeElementByValue($array, 'test99999999');
		$this->assertCount(count($array), $result);
	}

	public function testMergeArrayValues(): void {
		$array1 = [
			'a',
			'b',
			'c',
		];
		$array2 = [
			1,
			2,
			3,
		];

		$expected = [
			[
				'a',
				1,
			],
			[
				'b',
				2,
			],
			[
				'c',
				3,
			],
		];

		$result = Utils::mergeArrayValues($array1, $array2);

		$this->assertEquals($expected, $result);
	}

	public function testMergeArrayValuesWithDifferentSize(): void {
		$array1 = [
			'a',
			'b',
		];
		$array2 = [
			1,
			2,
			3,
		];

		$expected = [
			[
				'a',
				1,
			],
			[
				'b',
				2,
			],
			[
				null,
				3,
			],
		];

		$result = Utils::mergeArrayValues($array1, $array2);

		$this->assertEquals($expected, $result);
	}

	public function testGetPrevKey(): void {
		$testArray = [
			'a' => 'A',
			'b' => 'B',
		];
		$this->assertEquals('a', Utils::getPrevKey('b', $testArray));
		$this->assertEquals(false, Utils::getPrevKey('a', $testArray));
		$this->assertEquals(false, Utils::getPrevKey('c', $testArray));
	}

	public function testGetNextKey(): void {
		$testArray = [
			'a' => 'A',
			'b' => 'B',
		];
		$this->assertEquals('b', Utils::getNextKey('a', $testArray));
		$this->assertEquals(false, Utils::getNextKey('b', $testArray));
		$this->assertEquals(false, Utils::getNextKey('c', $testArray));
	}

	public function testApplyCallbackByPath(): void {
		$testArray = [
			'a'        => 'A',
			'b'        => 'B',
			'children' => [
				'test1',
				'test2',
				'test3',
			],
			'siblings' => [
				'left'  => 'sister',
				'right' => 'brother',
			],
		];

		$unsetDelegate = static function (&$array, $key) {
			unset($array[$key]);
		};

		$uppercaseDelegate = static function (&$array, $key) {
			$array[$key] = strtoupper($array[$key]);
		};

		// remove item
		Utils::applyCallbackByPath($testArray, [
			'a',
		], $unsetDelegate);
		$this->assertArrayNotHasKey('a', $testArray);

		// set uppercase on value
		Utils::applyCallbackByPath($testArray, [
			'siblings',
			'left',
		], $uppercaseDelegate);
		$this->assertEquals($testArray['siblings']['left'], 'SISTER');

		// Non existing value
		$result = Utils::applyCallbackByPath($testArray, [
			'siblings',
			'lefty',
		], $uppercaseDelegate);
		$this->assertNull($result);

		// Non existing value
		$result = Utils::applyCallbackByPath($testArray, [
			'siblings',
			'left',
			'test',
		], $uppercaseDelegate);
		$this->assertNull($result);

		// Non existing value
		$result = Utils::applyCallbackByPath($testArray, [
			'siblings',
			'left',
			'test',
			'test',
		], $uppercaseDelegate);
		$this->assertNull($result);
	}
}
