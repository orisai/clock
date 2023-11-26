<?php declare(strict_types = 1);

namespace Orisai\Clock;

use function func_get_args;
use function sleep;
use function trigger_error;
use function usleep;
use const E_USER_WARNING;

/**
 * @internal
 */
trait SystemSleepClock
{

	/**
	 * @infection-ignore-all
	 */
	public function sleep(
		int $seconds = 0,
		int $milliseconds = 0,
		int $microseconds = 0
	): void
	{
		if (func_get_args() === []) {
			trigger_error(
				'Arguments must be passed to method sleep(), otherwise it does not do anything.',
				E_USER_WARNING,
			);
		}

		if ($seconds > 0) {
			sleep($seconds);
		}

		if ($milliseconds > 0) {
			usleep($milliseconds * 1_000);
		}

		if ($microseconds > 0) {
			usleep($microseconds);
		}
	}

}
