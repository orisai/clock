<?php declare(strict_types = 1);

namespace Orisai\Clock;

use DateTimeImmutable;
use DateTimeZone;
use function date_default_timezone_get;
use function floor;
use function round;

final class FrozenClock implements Clock
{

	private DateTimeImmutable $dt;

	public function __construct(float $timestamp, ?DateTimeZone $timeZone = null)
	{
		[$seconds, $microseconds] = $this->getParts($timestamp);

		$this->dt = DateTimeImmutable::createFromFormat('U', (string) $seconds)
			->setTimezone($timeZone ?? new DateTimeZone(date_default_timezone_get()))
			->modify("+$microseconds usec");
	}

	public function now(): DateTimeImmutable
	{
		return clone $this->dt;
	}

	public function move(float $seconds): void
	{
		[$wholeSeconds, $microseconds] = $this->getParts($seconds);

		$this->dt = $this->dt
			->modify("$wholeSeconds sec")
			->modify("$microseconds usec");
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
