<h1 align="center">
	<img src="https://github.com/orisai/.github/blob/main/images/repo_title.png?raw=true" alt="Orisai"/>
	<br/>
	Clock
</h1>

<p align="center">
    Provider of current time for runtime and controllable time for testing
</p>

<p align="center">
	📄 Check out our <a href="docs/README.md">documentation</a>.
</p>

<p align="center">
	💸 If you like Orisai, please <a href="https://orisai.dev/sponsor">make a donation</a>. Thank you!
</p>

<p align="center">
	<a href="https://github.com/orisai/clock/actions?query=workflow%3Aci">
		<img src="https://github.com/orisai/clock/workflows/ci/badge.svg">
	</a>
	<a href="https://coveralls.io/r/orisai/clock">
		<img src="https://badgen.net/coveralls/c/github/orisai/clock/v1.x?cache=300">
	</a>
	<a href="https://dashboard.stryker-mutator.io/reports/github.com/orisai/clock/v1.x">
		<img src="https://badge.stryker-mutator.io/github.com/orisai/clock/v1.x">
	</a>
	<a href="https://packagist.org/packages/orisai/clock">
		<img src="https://badgen.net/packagist/dt/orisai/clock?cache=3600">
	</a>
	<a href="https://packagist.org/packages/orisai/clock">
		<img src="https://badgen.net/packagist/v/orisai/clock?cache=3600">
	</a>
	<a href="https://choosealicense.com/licenses/mpl-2.0/">
		<img src="https://badgen.net/badge/license/MPL-2.0/blue?cache=3600">
	</a>
<p>

##

```php
use Orisai\Clock\Clock;
use function Orisai\Clock\now;

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
		// Or
		$currentTime = now();
	}

}
```
