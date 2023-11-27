<?php declare(strict_types = 1);

namespace Tests\Orisai\Clock\Unit\Adapter;

use DateTimeZone;
use Orisai\Clock\Adapter\OrisaiToSymfonyClockAdapter;
use Orisai\Clock\FrozenClock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\ClockInterface;
use function interface_exists;

final class OrisaiToSymfonyClockAdapterTest extends TestCase
{

	public static function setUpBeforeClass(): void
	{
		// Workaround for minimal PHP version - Symfony requires PHP 8.1, we only 7.4
		if (!interface_exists(ClockInterface::class)) {
			self::markTestSkipped('symfony/clock is not installed.');
		}
	}

	public function testCurrentTime(): void
	{
		$innerClock = new FrozenClock(0.666);
		$clock = new OrisaiToSymfonyClockAdapter($innerClock);

		self::assertSame(
			'0.666000',
			$clock->now()->format('U.u'),
		);
	}

	public function testSleep(): void
	{
		$innerClock = new FrozenClock(0);
		$clock = new OrisaiToSymfonyClockAdapter($innerClock);

		self::assertSame(
			'0.000000',
			$clock->now()->format('U.u'),
		);

		$clock->sleep(1.234_567_8);
		self::assertSame(
			'1.234568',
			$clock->now()->format('U.u'),
		);
	}

	public function testTimeZone(): void
	{
		$innerClock = new FrozenClock(0, new DateTimeZone('UTC'));
		$clock = new OrisaiToSymfonyClockAdapter($innerClock);

		self::assertEquals(
			$clock->now()->getTimezone(),
			new DateTimeZone('UTC'),
		);

		$clock = $clock->withTimeZone(new DateTimeZone('Europe/Prague'));
		self::assertEquals(
			$clock->now()->getTimezone(),
			new DateTimeZone('Europe/Prague'),
		);
	}

}
