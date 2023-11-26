<?php declare(strict_types = 1);

namespace Tests\Orisai\Clock\Unit;

use DateTimeZone;
use Orisai\Clock\SystemClock;
use PHPUnit\Framework\TestCase;
use function date_default_timezone_get;
use function date_default_timezone_set;
use function error_get_last;
use function microtime;
use function usleep;

final class SystemClockTest extends TestCase
{

	public function testCurrentTime(): void
	{
		$clock = new SystemClock();

		$start = microtime(true);
		usleep(1);

		$now = $clock->now();

		usleep(1);
		$stop = microtime(true);

		self::assertGreaterThan($start, (float) $now->format('U.u'));
		self::assertLessThan($stop, (float) $now->format('U.u'));
	}

	public function testTimeChanges(): void
	{
		$clock = new SystemClock();

		$first = $clock->now()->format('U.u');
		usleep(1);
		$second = $clock->now()->format('U.u');

		self::assertGreaterThan($first, $second);
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testTimezone(): void
	{
		date_default_timezone_set('UTC');

		$clock = new SystemClock();
		self::assertSame(date_default_timezone_get(), $clock->now()->getTimezone()->getName());
		self::assertSame('UTC', $clock->now()->getTimezone()->getName());

		$clock = new SystemClock(new DateTimeZone('Europe/Prague'));
		self::assertSame('Europe/Prague', $clock->now()->getTimezone()->getName());
	}

	public function testSleep(): void
	{
		$clock = new SystemClock();
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
		$clock = new SystemClock();

		@$clock->sleep();

		self::assertSame(
			'Arguments must be passed to method sleep(), otherwise it does not do anything.',
			error_get_last()['message'] ?? null,
		);
	}

}
