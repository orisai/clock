<?php declare(strict_types = 1);

namespace Orisai\Clock\Adapter;

use DateTimeImmutable;
use DateTimeZone;
use Orisai\Clock\Clock;
use Symfony\Component\Clock\ClockInterface;
use function is_string;
use function round;

final class OrisaiToSymfonyClockAdapter implements ClockInterface
{

	private Clock $clock;

	private ?DateTimeZone $timeZone = null;

	public function __construct(Clock $clock)
	{
		$this->clock = $clock;
	}

	public function now(): DateTimeImmutable
	{
		$dt = $this->clock->now();

		if ($this->timeZone !== null) {
			$dt = $dt->setTimezone($this->timeZone);
		}

		return $dt;
	}

	public function sleep(float|int $seconds): void
	{
		$this->clock->sleep(0, 0, (int) round($seconds * 1E6));
	}

	public function withTimeZone(DateTimeZone|string $timezone): static
	{
		$clone = clone $this;
		$clone->timeZone = is_string($timezone) ? new DateTimeZone($timezone) : $timezone;

		return $clone;
	}

}
