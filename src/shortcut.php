<?php declare(strict_types = 1);

namespace Orisai\Clock;

use DateTimeImmutable;
use function function_exists;

if (!function_exists('Orisai\Clock\now')) {

	function now(): DateTimeImmutable
	{
		static $clock = null;

		if ($clock === null) {
			$clock = ClockHolder::getClock();
		}

		return $clock->now();
	}

}
