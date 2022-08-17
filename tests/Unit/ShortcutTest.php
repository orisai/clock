<?php declare(strict_types = 1);

namespace Tests\Orisai\Clock\Unit;

use Orisai\Clock\ClockHolder;
use Orisai\Clock\FrozenClock;
use Orisai\Exceptions\Logic\InvalidState;
use PHPUnit\Framework\TestCase;
use function Orisai\Clock\now;

/**
 * @runTestsInSeparateProcesses
 */
final class ShortcutTest extends TestCase
{

	public function testNotSet(): void
	{
		$this->expectException(InvalidState::class);
		$this->expectExceptionMessage(
			'Call Orisai\Clock\ClockHolder::setClock() to use Orisai\Clock\ClockHolder::getClock() ' .
			'and Orisai\Clock\now().',
		);

		now();
	}

	public function testSet(): void
	{
		require __DIR__ . '/../../src/shortcut.php'; // For code coverage

		$clock = new FrozenClock(1);
		ClockHolder::setClock($clock);

		self::assertSame('1.000000', now()->format('U.u'));
	}

}
