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

use StingerSoft\PhpCommons\Formatter\TimeFormatter;

class TimeFormatterTest extends \PHPUnit_Framework_TestCase {

	public function testPrettyPrintMicroTimeInterval() {
		$this->assertEquals('00:16:40', TimeFormatter::prettyPrintMicroTimeInterval(0, 1000));
		$this->assertEquals('00:00:10', TimeFormatter::prettyPrintMicroTimeInterval(0, 10));
		$this->assertEquals('00:00:10', TimeFormatter::prettyPrintMicroTimeInterval(0, 10.2));

	}
	

}