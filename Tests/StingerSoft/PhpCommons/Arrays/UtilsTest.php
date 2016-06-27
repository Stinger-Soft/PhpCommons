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

	public function testInsertElement(){
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
	
	public function testInsertElementWrongPosition(){
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
	
	public function testInsertElementAssoc(){
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
		$result = Utils::insertElement($array, array("three" => 3), 2);
		$this->assertEquals($expectedArray, $result);
	}
	
	
	public function testRemoveByValue(){
		$array = array(
			'test1',
			'test2',
			'test3',
			'test4',
		);
		$result = Utils::removeElementByValue($array, 'test2');
		$this->assertCount(count($array)-1, $result);
		$this->assertNotContains('test2', $result);
		
		$result = Utils::removeElementByValue($array, 'test99999999');
		$this->assertCount(count($array), $result);
	}
}