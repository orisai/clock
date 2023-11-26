<?php declare(strict_types = 1);

namespace Tests\Orisai\Clock\Unit\Adapter;

use DateTimeImmutable;
use Generator;
use Orisai\Clock\Adapter\ClockAdapterFactory;
use Orisai\Clock\Adapter\PsrToOrisaiClockAdapter;
use Orisai\Clock\FrozenClock;
use Orisai\Clock\SystemClock;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\ClockInterface as SymfonyClock;
use Symfony\Component\Clock\NativeClock;
use function class_exists;

final class ClockAdapterFactoryTest extends TestCase
{

	/**
	 * @param class-string<ClockInterface> $class
	 *
	 * @dataProvider provide
	 */
	public function test(ClockInterface $clock, string $class): void
	{
		self::assertInstanceOf(
			$class,
			ClockAdapterFactory::create($clock),
		);
	}

	public function provide(): Generator
	{
		yield [
			new class implements ClockInterface {

				public function now(): DateTimeImmutable
				{
					return new DateTimeImmutable();
				}

			},
			PsrToOrisaiClockAdapter::class,
		];

		yield [
			new FrozenClock(0),
			FrozenClock::class,
		];

		yield [
			new SystemClock(),
			SystemClock::class,
		];

		if (class_exists(SymfonyClock::class)) {
			yield [
				new NativeClock(),
				NativeClock::class,
			];
		}
	}

}
