<?php declare(strict_types = 1);

namespace Orisai\Clock\Adapter;

use Orisai\Clock\Clock;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\ClockInterface as SymfonyClock;

final class ClockAdapterFactory
{

	public static function create(ClockInterface $clock): Clock
	{
		if ($clock instanceof Clock) {
			return $clock;
		}

		if ($clock instanceof SymfonyClock) {
			return new SymfonyToOrisaiClockAdapter($clock);
		}

		return new PsrToOrisaiClockAdapter($clock);
	}

}
