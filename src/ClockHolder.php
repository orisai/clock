<?php declare(strict_types = 1);

namespace Orisai\Clock;

use Orisai\Exceptions\Logic\InvalidState;
use function sprintf;

final class ClockHolder
{

	private static ?Clock $clock = null;

	private function __construct()
	{
		// Static class
	}

	public static function setClock(Clock $clock): void
	{
		self::$clock = $clock;
	}

	public static function getClock(): Clock
	{
		if (self::$clock === null) {
			throw InvalidState::create()
				->withMessage(sprintf(
					'Call %s::setClock() to use %s() and %s\now().',
					self::class,
					__METHOD__,
					__NAMESPACE__,
				));
		}

		return self::$clock;
	}

}
