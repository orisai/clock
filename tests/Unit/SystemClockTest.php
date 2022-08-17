<?php declare(strict_types = 1);

namespace Tests\Orisai\Clock\Unit;

use DateTimeZone;
use Orisai\Clock\SystemClock;
use PHPUnit\Framework\TestCase;
use function date_default_timezone_get;
use function date_default_timezone_set;
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

}
