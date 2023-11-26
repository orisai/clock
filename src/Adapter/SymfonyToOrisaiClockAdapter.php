<?php declare(strict_types = 1);

namespace Orisai\Clock\Adapter;

use DateTimeImmutable;
use Orisai\Clock\Clock;
use Symfony\Component\Clock\ClockInterface;
use function func_get_args;
use function trigger_error;
use const E_USER_WARNING;

final class SymfonyToOrisaiClockAdapter implements Clock
{

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

		$this->clock->sleep(
			$seconds
			+ ($milliseconds / 1_000)
			+ ($microseconds / 1_000_000),
		);
	}

}
