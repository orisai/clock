<?php declare(strict_types = 1);

namespace Tests\Orisai\Clock\Unit;

use Orisai\Clock\ClockHolder;
use Orisai\Clock\FrozenClock;
use Orisai\Exceptions\Logic\InvalidState;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
final class ClockHolderTest extends TestCase
{

	public function testNotSet(): void
	{
		$this->expectException(InvalidState::class);
		$this->expectExceptionMessage(
			'Call Orisai\Clock\ClockHolder::setClock() to use Orisai\Clock\ClockHolder::getClock() ' .
			'and Orisai\Clock\now().',
		);

		ClockHolder::getClock();
	}

	public function testSet(): void
	{
		$clock = new FrozenClock(1);
		ClockHolder::setClock($clock);

		self::assertSame('1.000000', $clock->now()->format('U.u'));
	}

}
