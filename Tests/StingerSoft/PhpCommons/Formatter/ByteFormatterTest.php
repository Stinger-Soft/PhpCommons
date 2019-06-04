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
use StingerSoft\PhpCommons\Formatter\ByteFormatter;

class ByteFormatterTest extends TestCase {

	public function testPrettyPrintSize(): void {
		$this->assertEquals('100 B', ByteFormatter::prettyPrintSize(100, 2));
		$this->assertEquals('100 B', ByteFormatter::prettyPrintSize(100, 2, true));
		$this->assertEquals('1 kiB', ByteFormatter::prettyPrintSize(1024, 2));
		$this->assertEquals('1 kB', ByteFormatter::prettyPrintSize(1000, 2, true));
		$this->assertEquals('1.02 kB', ByteFormatter::prettyPrintSize(1024, 2, true));

		$this->assertEquals('1 MB', ByteFormatter::prettyPrintSize(1000 * 1000, 2, true));
		$this->assertEquals('1 MiB', ByteFormatter::prettyPrintSize(1024 * 1024, 2));
		$this->assertEquals('1.02 MB', ByteFormatter::prettyPrintSize(1024 * 1000, 2, true));
	}

	public function testPrettyPrintSizeInGerman(): void {
		$this->assertEquals('100 B', ByteFormatter::prettyPrintSize(100, 2, false, 'de'));
		$this->assertEquals('100 B', ByteFormatter::prettyPrintSize(100, 2, true, 'de'));
		$this->assertEquals('1 kiB', ByteFormatter::prettyPrintSize(1024, 2, false, 'de'));
		$this->assertEquals('1 kB', ByteFormatter::prettyPrintSize(1000, 2, true, 'de'));
		$this->assertEquals('1,02 kB', ByteFormatter::prettyPrintSize(1024, 2, true, 'de'));

		$this->assertEquals('1 MB', ByteFormatter::prettyPrintSize(1000 * 1000, 2, true, 'de'));
		$this->assertEquals('1 MiB', ByteFormatter::prettyPrintSize(1024 * 1024, 2, false, 'de'));
		$this->assertEquals('1,02 MB', ByteFormatter::prettyPrintSize(1024 * 1000, 2, true, 'de'));
	}
}