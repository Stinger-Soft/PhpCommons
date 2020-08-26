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

namespace StingerSoft\PhpCommons\String;

use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase {

	public function testStartsWith(): void {
		self::assertTrue(Utils::startsWith('testStartsWith', 'test'));
		self::assertTrue(Utils::startsWith('testStartsWith', ''));
		self::assertTrue(Utils::startsWith('', ''));
		self::assertTrue(Utils::startsWith('test', null));
		self::assertFalse(Utils::startsWith(null, 'test'));
		self::assertFalse(Utils::startsWith('testStartsWith', 'With'));
		self::assertTrue(Utils::startsWith(null, null));
	}

	public function testEndsWith(): void {
		self::assertTrue(Utils::endsWith('testStartsWith', 'With'));
		self::assertTrue(Utils::endsWith('testStartsWith', ''));
		self::assertTrue(Utils::endsWith('', ''));
		self::assertTrue(Utils::endsWith('With', null));
		self::assertFalse(Utils::endsWith(null, 'With'));
		self::assertFalse(Utils::endsWith('testStartsWith', 'test'));
		self::assertTrue(Utils::endsWith(null, null));
	}

	public function testCamelize(): void {
		self::assertEquals('handleTestresultSuccess', Utils::camelize('handle_testresult_success', '_', false));
		self::assertNotEquals('handleTestresultSuccess', Utils::camelize('handle_testresult_success', '_', true));
		self::assertEquals('HandleTestresultSuccess', Utils::camelize('handle_testresult_success', '_', true));
		self::assertNotEquals('HandleTestresultSuccess', Utils::camelize('handle_testresult_success', '_', false));
	}

	public function testHighlight(): void {
		self::assertEquals('This is an <em>awesome</em> text!', Utils::highlight('This is an awesome text!', 'awesome'));
		self::assertEquals('This is an <em>awesome</em> text!', Utils::highlight('This is an awesome text!', 'awe'));
		self::assertEquals('This is an awesome text!', Utils::highlight('This is an awesome text!', 'nono'));
	}

	public function testExcerpt(): void {
		self::assertEquals('Lorem ipsum dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', 'slayer', 100, '...'));
		self::assertEquals('Lorem ipsum dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', [
			'slayer',
			'metallica',
		], 100, '...'));
		self::assertEquals('...dolor...', Utils::excerpt('Lorem ipsum dolor sit amet', 'dolor', 0, '...'));
		self::assertEquals('Lorem ipsum dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', [
			'Lorem',
			'dolor',
		], 100, '...'));
		self::assertEquals('Lorem ipsum...', Utils::excerpt('Lorem ipsum dolor sit amet', [
			'Lorem',
			'dolor',
		], 0, '...'));
		self::assertEquals('...dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', [
			'dolor',
			'Lorem',
		], 0, '...'));
		self::assertEquals('Lorem ipsum...', Utils::excerpt('Lorem ipsum dolor sit amet', [
			'Lorem',
			'dolor',
		], 10, '...'));
		self::assertEquals('...dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', [
			'dolor',
			'Lorem',
		], 10, '...'));
		self::assertEquals('Lorem ipsum dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', [
			'Lorem',
			'dolor',
		], 20, '...'));
		self::assertEquals('Lorem ipsum dolor sit amet', Utils::excerpt('Lorem ipsum dolor sit amet', [
			'dolor',
			'Lorem',
		], 20, '...'));
	}

	public function testTruncate(): void {
		self::assertEquals('Lorem ipsum dolor...', Utils::truncate('Lorem ipsum dolor sit amet', 0, 20, '...'));
		self::assertEquals('Lorem ipsum dolor###', Utils::truncate('Lorem ipsum dolor sit amet', 0, 20, '###'));
		self::assertEquals('Lorem ipsum dolor...', Utils::truncate('Lorem ipsum dolor sit amet', 0, 20));
		self::assertEquals('orem ipsum dolor s...', Utils::truncate('Lorem ipsum dolor sit amet', 1, 21));
		self::assertEquals('orem ipsum dolor ...', Utils::truncate('Lorem ipsum dolor sit amet', 1, 20));
		self::assertEquals('Lorem ipsum dolor sit amet l...', Utils::truncate('Lorem ipsum dolor sit amet lorem ipsum dolor sit amet'));
		self::assertEquals('orem ipsum dolor sit amet lo...', Utils::truncate('Lorem ipsum dolor sit amet lorem ipsum dolor sit amet', 1));
		self::assertEquals('Lorem ipsum dolor sit amet', Utils::truncate('Lorem ipsum dolor sit amet'));
		self::assertEquals('', Utils::truncate(''));
		self::assertEquals('', Utils::truncate(null));
		self::assertEquals('orem ipsum dolor sit amet', Utils::truncate('Lorem ipsum dolor sit amet', 1));
	}

	public function testHashCode(): void {
		self::assertEquals(0, Utils::hashCode(null));
		self::assertEquals(0, Utils::hashCode(''));
		self::assertEquals(0, Utils::hashCode(false));
		self::assertEquals(0, Utils::hashCode(1));
		self::assertEquals(0, Utils::hashCode(1.0));
		self::assertEquals(0, Utils::hashCode([]));
		self::assertEquals(0, Utils::hashCode(new \stdClass()));

		self::assertEquals(-862545276, Utils::hashCode('Hello World'));
	}

	public function testInitialize(): void {
		self::assertEquals(null, Utils::initialize(null, false));
		self::assertEquals(null, Utils::initialize(null, true));
		self::assertEquals(null, Utils::initialize(null));

		self::assertEquals('', Utils::initialize(null, false));
		self::assertEquals('', Utils::initialize(null, true));
		self::assertEquals('', Utils::initialize(null));

		self::assertEquals('125', Utils::initialize('125', false));
		self::assertEquals('125', Utils::initialize('125', true));
		self::assertEquals('125', Utils::initialize('125'));

		self::assertEquals('Lid', Utils::initialize('Lorem ipsum dolor', false));
		self::assertEquals('LID', Utils::initialize('Lorem ipsum dolor', true));
		self::assertEquals('LID', Utils::initialize('Lorem ipsum dolor'));

		self::assertEquals('Lid', Utils::initialize('Lorem-ipsum-dolor', false));
		self::assertEquals('LID', Utils::initialize('Lorem-ipsum-dolor', true));
		self::assertEquals('LID', Utils::initialize('Lorem-ipsum-dolor'));

		self::assertEquals('Lid', Utils::initialize('Lorem.ipsum.dolor', false));
		self::assertEquals('LID', Utils::initialize('Lorem.ipsum.dolor', true));
		self::assertEquals('LID', Utils::initialize('Lorem.ipsum.dolor'));

		self::assertEquals('Lid', Utils::initialize('Lorem (ipsum) dolor', false));
		self::assertEquals('LID', Utils::initialize('Lorem (ipsum) dolor', true));
		self::assertEquals('LID', Utils::initialize('Lorem (ipsum) dolor'));
	}

	public function testMbSubstrReplace(): void {
		$var = 'ABCDEFGH:/MNRPQR/';

		/* These two examples replace all of $var with 'bob'. */
		self::assertEquals('bob', Utils::mb_substr_replace($var, 'bob', 0));
		self::assertEquals('bob', Utils::mb_substr_replace($var, 'bob', 0, strlen($var)));

		/* Insert 'bob' right at the beginning of $var. */
		self::assertEquals('bobABCDEFGH:/MNRPQR/', Utils::mb_substr_replace($var, 'bob', 0, 0));

		/* These next two replace 'MNRPQR' in $var with 'bob'. */
		self::assertEquals('ABCDEFGH:/bob/', Utils::mb_substr_replace($var, 'bob', 10, -1));
		self::assertEquals('ABCDEFGH:/bob/', Utils::mb_substr_replace($var, 'bob', -7, -1));

		/* Delete 'MNRPQR' from $var. */
		self::assertEquals('ABCDEFGH://', Utils::mb_substr_replace($var, '', 10, -1));

		$input = array('A: XXX', 'B: XXX', 'C: XXX');

		// A simple case: replace XXX in each string with YYY.
		self::assertEquals('A: YYY; B: YYY; C: YYY', implode('; ', Utils::mb_substr_replace($input, 'YYY', 3, 3)));

		// A more complicated case where each replacement is different.
		$replace = array('AAA', 'BBB', 'CCC');
		self::assertEquals('A: AAA; B: BBB; C: CCC',  implode('; ', Utils::mb_substr_replace($input, $replace, 3, 3)));

		// Replace a different number of characters each time.
		$length = array(1, 2, 3);
		self::assertEquals('A: AAAXX; B: BBBX; C: CCC', implode('; ', Utils::mb_substr_replace($input, $replace, 3, $length)));

	}
}