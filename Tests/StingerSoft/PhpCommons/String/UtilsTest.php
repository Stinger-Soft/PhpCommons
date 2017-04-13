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

class UtilsTest extends \PHPUnit_Framework_TestCase {

	public function testStartsWith() {
		$this->assertTrue(Utils::startsWith('testStartsWith', 'test'));
		$this->assertTrue(Utils::startsWith('testStartsWith', ''));
		$this->assertTrue(Utils::startsWith('', ''));
		$this->assertTrue(Utils::startsWith('test', null));
		$this->assertFalse(Utils::startsWith(null, 'test'));
		$this->assertFalse(Utils::startsWith('testStartsWith', 'With'));
	}

	public function testEndsWith() {
		$this->assertTrue(Utils::endsWith('testStartsWith', 'With'));
		$this->assertTrue(Utils::endsWith('testStartsWith', ''));
		$this->assertTrue(Utils::endsWith('', ''));
		$this->assertTrue(Utils::endsWith('With', null));
		$this->assertFalse(Utils::endsWith(null, 'With'));
		$this->assertFalse(Utils::endsWith('testStartsWith', 'test'));
	}

	public function testCamelize() {
		$this->assertEquals('handleTestresultSuccess', Utils::camelize('handle_testresult_success', '_', false));
		$this->assertNotEquals('handleTestresultSuccess', Utils::camelize('handle_testresult_success', '_', true));
		$this->assertEquals('HandleTestresultSuccess', Utils::camelize('handle_testresult_success', '_', true));
		$this->assertNotEquals('HandleTestresultSuccess', Utils::camelize('handle_testresult_success', '_', false));
	}

	public function testHighlight() {
		$this->assertEquals('This is an <em>awesome</em> text!', Utils::highlight('This is an awesome text!', 'awesome'));
		$this->assertEquals('This is an <em>awesome</em> text!', Utils::highlight('This is an awesome text!', 'awe'));
		$this->assertEquals('This is an awesome text!', Utils::highlight('This is an awesome text!', 'nono'));
	}

	public function testExcerpt() {
		$this->assertEquals('Lorem ipsum dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', 'slayer', 100, '...'));
		$this->assertEquals('Lorem ipsum dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', array(
			'slayer',
			'metallica' 
		), 100, '...'));
		$this->assertEquals('...dolor...', Utils::excerpt('Lorem ipsum dolor sit amet', 'dolor', 0, '...'));
		$this->assertEquals('Lorem ipsum dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', array(
			'Lorem',
			'dolor' 
		), 100, '...'));
		$this->assertEquals('Lorem ipsum...', Utils::excerpt('Lorem ipsum dolor sit amet', array(
			'Lorem',
			'dolor' 
		), 0, '...'));
		$this->assertEquals('...dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', array(
			'dolor',
			'Lorem' 
		), 0, '...'));
		$this->assertEquals('Lorem ipsum...', Utils::excerpt('Lorem ipsum dolor sit amet', array(
			'Lorem',
			'dolor' 
		), 10, '...'));
		$this->assertEquals('...dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', array(
			'dolor',
			'Lorem' 
		), 10, '...'));
		$this->assertEquals('Lorem ipsum dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', array(
			'Lorem',
			'dolor' 
		), 20, '...'));
		$this->assertEquals('Lorem ipsum dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', array(
			'dolor',
			'Lorem' 
		), 20, '...'));
	}

	public function testTruncate() {
		$this->assertEquals('Lorem ipsum dolor...', Utils::truncate('Lorem ipsum dolor sit amet', 0, 20, '...'));
		$this->assertEquals('Lorem ipsum dolor###', Utils::truncate('Lorem ipsum dolor sit amet', 0, 20, '###'));
		$this->assertEquals('Lorem ipsum dolor...', Utils::truncate('Lorem ipsum dolor sit amet', 0, 20));
		$this->assertEquals('orem ipsum dolor s...', Utils::truncate('Lorem ipsum dolor sit amet', 1, 21));
		$this->assertEquals('orem ipsum dolor ...', Utils::truncate('Lorem ipsum dolor sit amet', 1, 20));
		$this->assertEquals('Lorem ipsum dolor sit amet l...', Utils::truncate('Lorem ipsum dolor sit amet lorem ipsum dolor sit amet'));
		$this->assertEquals('orem ipsum dolor sit amet lo...', Utils::truncate('Lorem ipsum dolor sit amet lorem ipsum dolor sit amet', 1));
		$this->assertEquals('Lorem ipsum dolor sit amet', Utils::truncate('Lorem ipsum dolor sit amet'));
		$this->assertEquals('', Utils::truncate(''));
		$this->assertEquals('', Utils::truncate(null));
		$this->assertEquals('orem ipsum dolor sit amet', Utils::truncate('Lorem ipsum dolor sit amet', 1));
	}

	public function testRemoveDuplicatesByComparator() {
		$test = array(
			'a',
			'b',
			'A',
			'c' 
		);
		$result = array(
			'a',
			'b',
			'c' 
		);
		$this->assertEquals($result, Utils::removeDuplicatesByComparator($test, false));
		$result = array(
			0 => 'a',
			1 => 'b',
			3 => 'c' 
		);
		$this->assertEquals($result, Utils::removeDuplicatesByComparator($test, true));
	}
}