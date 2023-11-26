# Clock

Provides current time for runtime and controllable time for testing

[PSR-20](https://www.php-fig.org/psr/psr-20/) compatible

## Content

- [Setup](#setup)
- [Current time](#current-time)
- [Sleep](#sleep)
- [Shortcut function](#shortcut-function)
- [Clock](#clock-1)
	- [System clock](#system-clock)
	- [Frozen clock](#frozen-clock)
	- [Measurement clock](#measurement-clock)
	- [PSR to Orisai clock adapter](#psr-to-orisai-clock-adapter)
	- [Symfony to Orisai clock adapter](#symfony-to-orisai-clock-adapter)
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

## Current time

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

## Sleep

To prevent waiting periods in tests, replace calls to `sleep()` and `usleep()` functions with `Clock->sleep()` method.

```php
$clock->sleep(
	1, // Seconds
	2, // Milliseconds
	3, // Microseconds
);
```

Or with named arguments, just:

```php
$clock->sleep(microseconds: 10);
```

## Shortcut function

Get current time statically

```php
use function Orisai\Clock\now;

$currentTime = now(); // \DateTimeImmutable
```

## Clock

### System clock

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

### Frozen clock

For testing exact times. Does not change unless explicitly requested.

```php
use Orisai\Clock\FrozenClock;

$timestamp = 0.0;
$clock = new FrozenClock($timestamp);
$clock->now()->format('U.u'); // 0.000_000
```

Specify timezone (otherwise current timezone is used)

```php
use DateTimeZone;

$clock = new FrozenClock(0, new DateTimeZone('UTC'));
```

[Sleeping](#sleep) does not put thread to sleep but just moves timestamp of clock's internal `DateTimeImmutable`.

### Measurement clock

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

### PSR to Orisai clock adapter

Decorate any `Psr\Clock\ClockInterface` implementation to conform interface `Orisai\Clock\Clock`.

```php
use Orisai\Clock\Adapter\PsrToOrisaiClockAdapter;

$clock = new PsrToOrisaiClockAdapter(new ExamplePsrClock());
```

### Symfony to Orisai clock adapter

Decorate any `Symfony\Component\Clock\ClockInterface` implementation to conform interface `Orisai\Clock\Clock`.

```php
use Orisai\Clock\Adapter\SymfonyToOrisaiClockAdapter;
use Symfony\Component\Clock\NativeClock;

$clock = new SymfonyToOrisaiClockAdapter(new NativeClock());
```

## Integrations and extensions

- [Nette](https://github.com/nette) integration - [orisai/nette-clock](https://github.com/orisai/nette-clock)
