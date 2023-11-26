<?php declare(strict_types = 1);

namespace Orisai\Clock\Adapter;

use DateTimeImmutable;
use Orisai\Clock\Clock;
use Orisai\Clock\SystemSleepClock;
use Psr\Clock\ClockInterface;
use function func_get_args;
use function trigger_error;
use const E_USER_WARNING;

final class PsrToOrisaiClockAdapter implements Clock
{

	use SystemSleepClock {
		sleep as systemSleep;
	}

	private ClockInterface $clock;

	public function __construct(ClockInterface $clock)
	{
		$this->clock = $clock;
	}

	public function now(): DateTimeImmutable
	{
		return $this->clock->now();
	}

	/**
	 * @infection-ignore-all
	 */
	public function sleep(int $seconds = 0, int $milliseconds = 0, int $microseconds = 0): void
	{
		if (func_get_args() === []) {
			trigger_error(
				'Arguments must be passed to method sleep(), otherwise it does not do anything.',
				E_USER_WARNING,
			);
		}

		if ($this->clock instanceof Clock) {
			$this->clock->sleep($seconds, $milliseconds, $microseconds);
		} else {
			$this->systemSleep($seconds, $milliseconds, $microseconds);
		}
	}

}
