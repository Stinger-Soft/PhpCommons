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
namespace StingerSoft\PhpCommons\Integer;

use StingerSoft\PhpCommons\Integer\Utils;

class UtilsTest extends \PHPUnit_Framework_TestCase {
	
	
	public function testIntCmp() {
		$this->assertGreaterThan(0, Utils::intcmp(1, 0));
		$this->assertLessThan(0, Utils::intcmp(0, 1));
		$this->assertEquals(0, Utils::intcmp(0, 0));
	}
}