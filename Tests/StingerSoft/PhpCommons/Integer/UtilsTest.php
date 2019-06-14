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
namespace StingerSoft\PhpCommons\Integer;

use PHPUnit\Framework\TestCase;
use stdClass;

class UtilsTest extends TestCase {
	
	
	public function testIntCmp(): void {
		$this->assertGreaterThan(0, Utils::intcmp(1, 0));
		$this->assertLessThan(0, Utils::intcmp(0, 1));
		$this->assertEquals(0, Utils::intcmp(0, 0));
		
		$this->assertGreaterThan(0, Utils::intcmp(1, null));
		$this->assertLessThan(0, Utils::intcmp(null, 1));
		$this->assertEquals(0, Utils::intcmp(null, null));
	}

	public function testIsInteger() : void {
		$this->assertFalse(Utils::isInteger(false));
		$this->assertFalse(Utils::isInteger(null));
		$this->assertFalse(Utils::isInteger([]));
		$this->assertFalse(Utils::isInteger(''));
		$this->assertFalse(Utils::isInteger('a'));
		$this->assertFalse(Utils::isInteger(new stdClass()));
		$this->assertFalse(Utils::isInteger('1,1'));
		$this->assertFalse(Utils::isInteger('1.1'));
		$this->assertFalse(Utils::isInteger('1e01'));
		$this->assertFalse(Utils::isInteger('-1,1'));
		$this->assertFalse(Utils::isInteger('-1.1'));
		$this->assertFalse(Utils::isInteger('-1e01'));

		$this->assertTrue(Utils::isInteger(0));
		$this->assertTrue(Utils::isInteger(0.0));
		$this->assertTrue(Utils::isInteger('0'));
		$this->assertTrue(Utils::isInteger(-2));
		$this->assertTrue(Utils::isInteger(-2.0));
		$this->assertTrue(Utils::isInteger('-2'));
	}
}