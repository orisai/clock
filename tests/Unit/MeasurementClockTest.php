<?php declare(strict_types = 1);

namespace Tests\Orisai\Clock\Unit;

use DateTimeZone;
use Orisai\Clock\MeasurementClock;
use PHPUnit\Framework\TestCase;
use function date_default_timezone_get;
use function date_default_timezone_set;
use function error_get_last;
use function range;
use function usleep;

final class MeasurementClockTest extends TestCase
{

	public function testCurrentTime(): void
	{
		// Run multiple times to reliably execute all variants
		foreach (range(0, 1_000) as $i) {
			$clock = new MeasurementClock();

			$start = (float) $clock->now()->format('U.u');
			$now = $clock->now();
			$stop = (float) $clock->now()->format('U.u');

			self::assertGreaterThan($start - 0.01, (float) $now->format('U.u'));
			self::assertLessThan($stop + 0.01, (float) $now->format('U.u'));
		}
	}

	public function testTimeChanges(): void
	{
		$clock = new MeasurementClock();

		$first = $clock->now()->format('U.u');
		usleep(100);
		$second = $clock->now()->format('U.u');

		self::assertGreaterThan($first, $second);
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testTimezone(): void
	{
		date_default_timezone_set('UTC');

		$clock = new MeasurementClock();
		self::assertSame(date_default_timezone_get(), $clock->now()->getTimezone()->getName());
		self::assertSame('UTC', $clock->now()->getTimezone()->getName());

		$clock = new MeasurementClock(new DateTimeZone('Europe/Prague'));
		self::assertSame('Europe/Prague', $clock->now()->getTimezone()->getName());
	}

	public function testSleep(): void
	{
		$clock = new MeasurementClock();
		$initTime = (float) $clock->now()->format('U.u');

		$clock->sleep(1);
		self::assertGreaterThan(
			$initTime + 1,
			(float) $clock->now()->format('U.u'),
		);

		$clock->sleep(0, 10);
		self::assertGreaterThan(
			$initTime + 0.010,
			(float) $clock->now()->format('U.u'),
		);

		$clock->sleep(0, 0, 1_000);
		self::assertGreaterThan(
			$initTime + 0.010,
			(float) $clock->now()->format('U.u'),
		);
	}

	public function testSleepNoArgs(): void
	{
		$clock = new MeasurementClock();

		@$clock->sleep();

		self::assertSame(
			'Arguments must be passed to method sleep(), otherwise it does not do anything.',
			error_get_last()['message'] ?? null,
		);
	}

}
