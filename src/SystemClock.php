<?php declare(strict_types = 1);

namespace Orisai\Clock;

use DateTimeImmutable;
use DateTimeZone;
use function date_default_timezone_get;

final class SystemClock implements Clock
{

	private DateTimeZone $timeZone;

	public function __construct(?DateTimeZone $timeZone = null)
	{
		$this->timeZone = $timeZone ?? new DateTimeZone(date_default_timezone_get());
	}

	public function now(): DateTimeImmutable
	{
		return (new DateTimeImmutable('now'))
			->setTimezone($this->timeZone);
	}

}
