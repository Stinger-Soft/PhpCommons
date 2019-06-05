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

use DateInterval;
use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use StingerSoft\PhpCommons\Formatter\TimeFormatter;

class TimeFormatterTest extends TestCase {

	public function testPrettyPrintMicroTimeInterval(): void {
		$this->assertEquals('00:16:40', TimeFormatter::prettyPrintMicroTimeInterval(0, -1000));
		$this->assertEquals('00:00:10', TimeFormatter::prettyPrintMicroTimeInterval(0, -10));
		$this->assertEquals('00:00:10', TimeFormatter::prettyPrintMicroTimeInterval(0, -10.2));
		$this->assertEquals('02:46:40', TimeFormatter::prettyPrintMicroTimeInterval(0, -10000));
		$this->assertEquals('277:46:40', TimeFormatter::prettyPrintMicroTimeInterval(0, -1000000));

		$this->assertEquals('00:00:00', TimeFormatter::prettyPrintMicroTimeInterval(0, 0));

		$this->assertEquals('00:16:40', TimeFormatter::prettyPrintMicroTimeInterval(0, 1000));
		$this->assertEquals('00:00:10', TimeFormatter::prettyPrintMicroTimeInterval(0, 10));
		$this->assertEquals('00:00:10', TimeFormatter::prettyPrintMicroTimeInterval(0, 10.2));
		$this->assertEquals('02:46:40', TimeFormatter::prettyPrintMicroTimeInterval(0, 10000));
		$this->assertEquals('277:46:40', TimeFormatter::prettyPrintMicroTimeInterval(0, 1000000));
	}

	/**
	 * @dataProvider invalidGetRelativeTimeFromDataProvider
	 * @param int|DateTime      $fromValue
	 *            the start time, either as <code>DateTime</code> object or as integer expressing a unix timestamp,
	 *            used for calculating the relative difference to the given <code>to</code> parameter.
	 * @param int|DateTime|null $toValue
	 *            the end time, either as <code>DateTime</code> object, a unix timestamp or <code>null</code> used for
	 *            calculating the difference to the given <code>from</code> parameter. In case <code>null</code> is
	 *            provided, the current timestamp will be used (utilizing <code>time()</code>).
	 */
	public function testGetRelativeTimeDifferenceWithInvalidValues($fromValue, $toValue): void {
		$this->expectException(InvalidArgumentException::class);
		TimeFormatter::getRelativeTimeDifference($fromValue, $toValue);
	}

	public function testGetRelativeTimeDifferenceInSeconds(): void {
		$from = time() - 1;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_SECONDS, 1);

		$from = time() - 1;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_SECONDS, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('PT1S'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_SECONDS, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('PT1S'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_SECONDS, 1);

		$from = time() - 59;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_SECONDS, 59);

		$from = time() - 59;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_SECONDS, 59);

		$from = new DateTime();
		$from->sub(new DateInterval('PT59S'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_SECONDS, 59);

		$from = new DateTime();
		$from->sub(new DateInterval('PT59S'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_SECONDS, 59);
	}

	public function testGetRelativeTimeDifferenceInMinutes(): void {
		$from = time() - 60;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MINUTES, 1);

		$from = time() - 60;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MINUTES, 1);

		$from = time() - 60 * 59;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MINUTES, 59);

		$from = time() - 60 * 59;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MINUTES, 59);

		$from = new DateTime();
		$from->sub(new DateInterval('PT1M'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MINUTES, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('PT1M'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MINUTES, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('PT59M'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MINUTES, 59);

		$from = new DateTime();
		$from->sub(new DateInterval('PT59M'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MINUTES, 59);
	}

	public function testGetRelativeTimeDifferenceInHours(): void {
		$from = time() - 60 * 60;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_HOURS, 1);

		$from = time() - 60 * 60;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_HOURS, 1);

		$from = time() - 60 * 60 * 23;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_HOURS, 23);

		$from = time() - 60 * 60 * 23;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_HOURS, 23);

		$from = new DateTime();
		$from->sub(new DateInterval('PT1H'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_HOURS, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('PT1H'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_HOURS, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('PT23H'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_HOURS, 23);

		$from = new DateTime();
		$from->sub(new DateInterval('PT23H'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_HOURS, 23);
	}

	public function testGetRelativeTimeDifferenceInDays(): void {
		$from = time() - 60 * 60 * 24;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_DAYS, 1);

		$from = time() - 60 * 60 * 24;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_DAYS, 1);

		$from = time() - 60 * 60 * 24 * 6;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_DAYS, 6);

		$from = time() - 60 * 60 * 24 * 6;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_DAYS, 6);

		$from = new DateTime();
		$from->sub(new DateInterval('P1D'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_DAYS, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('P1D'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_DAYS, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('P6D'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_DAYS, 6);

		$from = new DateTime();
		$from->sub(new DateInterval('P6D'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_DAYS, 6);

		//Summertime
		$from = DateTime::createFromFormat('Y-m-d', '2019-04-04', new DateTimeZone('Europe/Berlin'));
		$from->sub(new DateInterval('P7D'));
		$to = DateTime::createFromFormat('Y-m-d', '2019-04-04', new DateTimeZone('Europe/Berlin'));
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_DAYS, 7);

	}

	public function testGetRelativeTimeDifferenceInWeeks(): void {
		$from = time() - 60 * 60 * 24 * 7;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_WEEKS, 1);

		$from = time() - 60 * 60 * 24 * 7;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_WEEKS, 1);

		$from = time() - 60 * 60 * 24 * 29;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_WEEKS, 4);

		$from = time() - 60 * 60 * 24 * 29;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_WEEKS, 4);

		$from = new DateTime();
		$from->sub(new DateInterval('P7DT1H'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_WEEKS, 1);

		$from = DateTime::createFromFormat('Y-m-d', '2019-07-01');
		$from->sub(new DateInterval('P7D'));
		$to = DateTime::createFromFormat('Y-m-d', '2019-07-01');
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_WEEKS, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('P29D'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_WEEKS, 4);

		$from = DateTime::createFromFormat('Y-m-d', '2019-07-01');
		$from->sub(new DateInterval('P29D'));
		$to = DateTime::createFromFormat('Y-m-d', '2019-07-01');
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_WEEKS, 4);
	}

	public function testGetRelativeTimeDifferenceInMonths(): void {
		$from = time() - 60 * 60 * 24 * 30;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MONTHS, 1);

		$from = time() - 60 * 60 * 24 * 30;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MONTHS, 1);

		$from = time() - 60 * 60 * 24 * 30 * 11;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MONTHS, 11);

		$from = time() - 60 * 60 * 24 * 30 * 11;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MONTHS, 11);

		$from = new DateTime();
		$from->sub(new DateInterval('P1M'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MONTHS, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('P1M'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MONTHS, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('P11M'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MONTHS, 11);

		$from = new DateTime();
		$from->sub(new DateInterval('P11M'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_MONTHS, 11);
	}

	public function testGetRelativeTimeDifferenceInYears(): void {
		$from = time() - 60 * 60 * 24 * 365;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_YEARS, 1);

		$from = time() - 60 * 60 * 24 * 365;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_YEARS, 1);

		$from = time() - 60 * 60 * 24 * 365 * 36;
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_YEARS, 36);

		$from = time() - 60 * 60 * 24 * 365 * 36;
		$to = time();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_YEARS, 36);

		$from = new DateTime();
		$from->sub(new DateInterval('P1Y'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_YEARS, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('P1Y'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_YEARS, 1);

		$from = new DateTime();
		$from->sub(new DateInterval('P36Y'));
		$to = null;
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_YEARS, 36);

		$from = new DateTime();
		$from->sub(new DateInterval('P36Y'));
		$to = new DateTime();
		$this->validateDiff(TimeFormatter::getRelativeTimeDifference($from, $to), TimeFormatter::UNIT_YEARS, 36);
	}

	protected function validateDiff($diff, $expectedUnit, $expectedDiff): void {
		$this->assertCount(2, $diff);
		$this->assertEquals($expectedUnit, $diff[1]);
		$this->assertEquals($expectedDiff, $diff[0]);
	}

	public function invalidGetRelativeTimeFromDataProvider(): array {
		return [
			[
				true, true,
			],
			[
				null, null,
			],
			[
				'test', 'test',
			],
			[
				new stdClass(), new stdClass(),
			],
			[
				time(), false,
			],
			[
				time(), 'test',
			],
			[
				time(), new stdClass(),
			],
			[
				new DateTime(), false,
			],
			[
				new DateTime(), 'test',
			],
			[
				new DateTime(), new stdClass(),
			],
		];
	}
}