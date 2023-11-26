<?php declare(strict_types = 1);

namespace Orisai\Clock;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

interface Clock extends ClockInterface
{

	public function now(): DateTimeImmutable;

	/**
	 * Sleep for given amount of time.
	 * Negative and zero sleep times are ignored.
	 * If no arguments are given, warning is triggered.
	 */
	public function sleep(
		int $seconds = 0,
		int $milliseconds = 0,
		int $microseconds = 0
	): void;

}
