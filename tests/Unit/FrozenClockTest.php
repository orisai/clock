<?php declare(strict_types = 1);

namespace Tests\Orisai\Clock\Unit;

use DateTimeZone;
use Orisai\Clock\FrozenClock;
use PHPUnit\Framework\TestCase;
use function date_default_timezone_get;
use function date_default_timezone_set;
use function usleep;

final class FrozenClockTest extends TestCase
{

	public function testFrozenTime(): void
	{
		$clock = new FrozenClock(1);
		$now = $clock->now()->format('U.u');
		self::assertSame('1.000000', $now);

		// 100_000 µs
		$clock = new FrozenClock(1.1);
		$now = $clock->now()->format('U.u');
		self::assertSame('1.100000', $now);

		// 1 µs
		$clock = new FrozenClock(1.000_001);
		$now = $clock->now()->format('U.u');
		self::assertSame('1.000001', $now);

		// Round up
		$clock = new FrozenClock(1.000_000_5);
		$now = $clock->now()->format('U.u');
		self::assertSame('1.000001', $now);

		// Round down
		$clock = new FrozenClock(1.000_000_49);
		$now = $clock->now()->format('U.u');
		self::assertSame('1.000000', $now);
	}

	public function testTimeDoesNotChange(): void
	{
		$clock = new FrozenClock(1);

		$first = $clock->now()->format('U.u');
		usleep(1);
		$second = $clock->now()->format('U.u');

		self::assertSame($first, $second);
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testTimezone(): void
	{
		date_default_timezone_set('UTC');

		$clock = new FrozenClock(1);
		self::assertSame(date_default_timezone_get(), $clock->now()->getTimezone()->getName());
		self::assertSame('UTC', $clock->now()->getTimezone()->getName());

		$clock = new FrozenClock(1, new DateTimeZone('Europe/Prague'));
		self::assertSame('Europe/Prague', $clock->now()->getTimezone()->getName());
	}

	public function testClone(): void
	{
		$clock = new FrozenClock(1);

		self::assertEquals($clock->now(), $clock->now());
		self::assertNotSame($clock->now(), $clock->now());
	}

	public function testMove(): void
	{
		$clock = new FrozenClock(10);

		$now = $clock->now()->format('U.u');
		self::assertSame('10.000000', $now);

		// No change
		$clock->move(0.0);
		$now = $clock->now()->format('U.u');
		self::assertSame('10.000000', $now);

		// 1 seconds
		$clock->move(1);
		$now = $clock->now()->format('U.u');
		self::assertSame('11.000000', $now);

		// 0.5 seconds
		$clock->move(0.5);
		$now = $clock->now()->format('U.u');
		self::assertSame('11.500000', $now);

		// 0.49 seconds
		$clock->move(0.49);
		$now = $clock->now()->format('U.u');
		self::assertSame('11.990000', $now);

		// 100_000 µs
		$clock->move(0.1);
		$now = $clock->now()->format('U.u');
		self::assertSame('12.090000', $now);

		// 1 µs
		$clock->move(0.000_001);
		$now = $clock->now()->format('U.u');
		self::assertSame('12.090001', $now);

		// Round up to 1 µs
		$clock->move(0.000_000_5);
		$now = $clock->now()->format('U.u');
		self::assertSame('12.090002', $now);

		// Round down to 0 µs
		$clock->move(0.000_000_49);
		$now = $clock->now()->format('U.u');
		self::assertSame('12.090002', $now);

		// Minus
		$clock->move(-1.1);
		$now = $clock->now()->format('U.u');
		self::assertSame('10.990002', $now);
	}

}
