<?php declare(strict_types = 1);

namespace Tests\Orisai\Clock\Unit\Adapter;

use DateTimeImmutable;
use Orisai\Clock\Adapter\PsrToOrisaiClockAdapter;
use Orisai\Clock\FrozenClock;
use Orisai\Clock\SystemClock;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use function error_get_last;
use function microtime;
use function usleep;

final class PsrToOrisaiClockAdapterTest extends TestCase
{

	public function testCurrentTime(): void
	{
		$clock = new PsrToOrisaiClockAdapter(new SystemClock());

		$start = microtime(true);
		usleep(1);

		$now = $clock->now();

		usleep(1);
		$stop = microtime(true);

		self::assertGreaterThan($start, (float) $now->format('U.u'));
		self::assertLessThan($stop, (float) $now->format('U.u'));
	}

	public function testSleepWithOrisaiClock(): void
	{
		$clock = new PsrToOrisaiClockAdapter(new FrozenClock(10));

		self::assertSame(
			'10.000000',
			$clock->now()->format('U.u'),
		);

		$clock->sleep(1, 2, 3);
		self::assertSame(
			'11.002003',
			$clock->now()->format('U.u'),
		);
	}

	public function testSleepWithPsrClock(): void
	{
		$clock = new PsrToOrisaiClockAdapter(new class implements ClockInterface {

			public function now(): DateTimeImmutable
			{
				return new DateTimeImmutable();
			}

		});

		$initTime = (float) $clock->now()->format('U.u');

		$clock->sleep(1, 20, 3_000);
		self::assertGreaterThan(
			$initTime + 1.023,
			(float) $clock->now()->format('U.u'),
		);
	}

	public function testSleepNoArgs(): void
	{
		$clock = new PsrToOrisaiClockAdapter(new FrozenClock(0));

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
