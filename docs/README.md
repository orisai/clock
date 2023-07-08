# Clock

Provides current time for runtime and controllable time for testing

[PSR-20](https://www.php-fig.org/psr/psr-20/) compatible

## Content

- [Setup](#setup)
- [Clock usage](#clock-usage)
- [Shortcut function](#shortcut-function)
- [System clock](#system-clock)
- [Frozen clock](#frozen-clock)
- [Measurement clock](#measurement-clock)
- [Integrations and extensions](#integrations-and-extensions)

## Setup

Install with [Composer](https://getcomposer.org)

```sh
composer require orisai/clock
```

Create [Clock](#clock-usage) instance and setup for [shortcut function](#shortcut-function) usage

```php
use Orisai\Clock\SystemClock;
use Orisai\Clock\ClockHolder;

$clock = new SystemClock();
ClockHolder::setClock($clock);
```

## Clock usage

Request `Clock` interface and get current time (or `Psr\Clock\ClockInterface` for `psr/clock` compatibility)

```php
use Orisai\Clock\Clock;

class ExampleService
{

	private Clock $clock;

	public function __construct(Clock $clock)
	{
		$this->clock = $clock;
	}

	public function doSomething(): void
	{
		$currentTime = $this->clock->now();
	}

}
```

## Shortcut function

Get current time statically

```php
use function Orisai\Clock\now;

$currentTime = now(); // \DateTimeImmutable
```

## System clock

For standard usage in application runtime. Returns same time as `new DateTimeImmutable('now')` would.

```php
use Orisai\Clock\SystemClock;

$clock = new SystemClock();
```

Specify timezone (otherwise current timezone is used)

```php
use DateTimeZone;

$clock = new SystemClock(new DateTimeZone('UTC'));
```

## Frozen clock

For testing exact times. Does not change unless explicitly requested.

```php
use Orisai\Clock\FrozenClock;

$timestamp = 0.0;
$clock = new FrozenClock($timestamp);
$clock->now()->format('U.u'); // 0.000_000
```

Move in time (with microsecond accuracy)

```php
$timestamp = 42.333;
$clock = new FrozenClock($timestamp);
$clock->now()->format('U.u'); // 42.333_000 ($timestamp)

$move = 624.333_666;
$clock->move($move);
$clock->now()->getTimestamp(); // 666.666_666 ($timestamp + $move)
```

Specify timezone (otherwise current timezone is used)

```php
use DateTimeZone;

$clock = new FrozenClock(0, new DateTimeZone('UTC'));
```

## Measurement clock

For accurate measurement of time. Unlike standard [system clock](#system-clock) is not affected by clock drifting
(which is wanted for normal usage, but makes time measuring unreliable).

```php
use Orisai\Clock\MeasurementClock;

$clock = new MeasurementClock();
```

Specify timezone (otherwise current timezone is used)

```php
use DateTimeZone;

$clock = new MeasurementClock(new DateTimeZone('UTC'));
```

## Integrations and extensions

- [Nette](https://github.com/nette) integration - [orisai/nette-clock](https://github.com/orisai/nette-clock)
