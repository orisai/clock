<?php declare(strict_types = 1);

namespace Orisai\Clock;

use DateTimeImmutable;
use DateTimeZone;
use function date_default_timezone_get;
use function gettimeofday;
use function hrtime;

final class MeasurementClock implements Clock
{

	private DateTimeZone $timeZone;

	private int $secondsOffset;

	private int $microsecondsOffset;

	public function __construct(?DateTimeZone $timeZone = null)
	{
		$this->timeZone = $timeZone ?? new DateTimeZone(date_default_timezone_get());

		[$seconds, $microseconds] = hrtime();
		$currentTime = gettimeofday();
		/** @infection-ignore-all */
		$this->secondsOffset = $currentTime['sec'] - $seconds;
		/** @infection-ignore-all */
		$this->microsecondsOffset = $currentTime['usec'] - (int) ($microseconds / 1_000);
	}

	public function now(): DateTimeImmutable
	{
		[$seconds, $microseconds] = hrtime();

		$computedSeconds = $seconds + $this->secondsOffset;
		/** @infection-ignore-all */
		$computedMicroseconds = (int) ($microseconds / 1_000) + $this->microsecondsOffset;

		return DateTimeImmutable::createFromFormat(
			'U',
			(string) $computedSeconds,
		)->setTimezone($this->timeZone)
			->modify("+$computedMicroseconds usec");
	}

}
