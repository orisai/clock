<?php declare(strict_types = 1);

namespace Orisai\Clock;

use DateTimeImmutable;
use DateTimeZone;
use function date_default_timezone_get;
use function floor;
use function func_get_args;
use function round;
use function trigger_error;
use const E_USER_WARNING;

final class FrozenClock implements Clock
{

	private DateTimeImmutable $dt;

	public function __construct(float $timestamp, ?DateTimeZone $timeZone = null)
	{
		[$seconds, $microseconds] = $this->getParts($timestamp);

		$this->dt = DateTimeImmutable::createFromFormat('U', (string) $seconds)
			->setTimezone($timeZone ?? new DateTimeZone(date_default_timezone_get()))
			->modify("+$microseconds microsecond");
	}

	public function now(): DateTimeImmutable
	{
		return clone $this->dt;
	}

	/**
	 * @deprecated Method move() will be removed in orisai/clock v2.0. Use method sleep() instead.
	 */
	public function move(float $seconds): void
	{
		[$wholeSeconds, $microseconds] = $this->getParts($seconds);

		$this->dt = $this->dt
			->modify("$wholeSeconds second")
			->modify("$microseconds microsecond");
	}

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
			$this->dt = $this->dt->modify("$seconds second");
		}

		if ($milliseconds > 0) {
			$this->dt = $this->dt->modify("$milliseconds millisecond");
		}

		if ($microseconds > 0) {
			$this->dt = $this->dt->modify("$microseconds microsecond");
		}
	}

	/**
	 * @return array{float, float}
	 */
	private function getParts(float $seconds): array
	{
		/** @infection-ignore-all */
		$wholeSeconds = floor($seconds);
		$microseconds = round(($seconds - $wholeSeconds) * 1E6);

		return [$wholeSeconds, $microseconds];
	}

}
