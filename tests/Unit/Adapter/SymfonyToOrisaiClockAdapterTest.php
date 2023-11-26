<?php declare(strict_types = 1);

namespace Tests\Orisai\Clock\Unit\Adapter;

use DateTimeImmutable;
use Orisai\Clock\Adapter\SymfonyToOrisaiClockAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\NativeClock;
use function error_get_last;
use function interface_exists;
use function microtime;
use function usleep;

final class SymfonyToOrisaiClockAdapterTest extends TestCase
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
		$clock = new SymfonyToOrisaiClockAdapter(new NativeClock());

		$start = microtime(true);
		usleep(1);

		$now = $clock->now();

		usleep(1);
		$stop = microtime(true);

		self::assertGreaterThan($start, (float) $now->format('U.u'));
		self::assertLessThan($stop, (float) $now->format('U.u'));
	}

	public function testSleep(): void
	{
		$clock = new SymfonyToOrisaiClockAdapter(
			new MockClock(DateTimeImmutable::createFromFormat('U', '10')),
		);

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

	public function testSleepNoArgs(): void
	{
		$clock = new SymfonyToOrisaiClockAdapter(
			new MockClock(DateTimeImmutable::createFromFormat('U', '0')),
		);

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
