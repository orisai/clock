<?php declare(strict_types = 1);

namespace Tests\Orisai\Clock\Unit;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Generator;
use Orisai\Clock\FrozenClock;
use PHPUnit\Framework\TestCase;
use function date_default_timezone_get;
use function date_default_timezone_set;
use function error_get_last;
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

	/**
	 * @param int|DateTimeInterface $datetime
	 *
	 * @dataProvider provideTimestampVariants
	 */
	public function testTimestampVariants($datetime, int $timestamp): void
	{
		$clock = new FrozenClock($datetime);
		self::assertSame($timestamp, $clock->now()->getTimestamp());
	}

	public function provideTimestampVariants(): Generator
	{
		yield [
			1,
			1,
		];

		yield [
			DateTimeImmutable::createFromFormat('U', '1'),
			1,
		];

		yield [
			DateTime::createFromFormat('U', '1'),
			1,
		];
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

	public function testSleep(): void
	{
		$clock = new FrozenClock(10);

		self::assertSame(
			'10.000000',
			$clock->now()->format('U.u'),
		);

		$clock->sleep(1);
		self::assertSame(
			'11.000000',
			$clock->now()->format('U.u'),
		);

		$clock->sleep(0, 2);
		self::assertSame(
			'11.002000',
			$clock->now()->format('U.u'),
		);

		$clock->sleep(0, 0, 3);
		self::assertSame(
			'11.002003',
			$clock->now()->format('U.u'),
		);

		$clock->sleep(3, 2, 1);
		self::assertSame(
			'14.004004',
			$clock->now()->format('U.u'),
		);
	}

	public function testSleepNoArgs(): void
	{
		$clock = new FrozenClock(0);

		@$clock->sleep();

		self::assertSame(
			'Arguments must be passed to method sleep(), otherwise it does not do anything.',
			error_get_last()['message'] ?? null,
		);
		self::assertSame(
			'0.000000',
			$clock->now()->format('U.u'),
		);
	}

}
