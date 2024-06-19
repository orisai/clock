<?php declare(strict_types = 1);

// This is needed because symfony/clock requires newer PHP version than us

$requiredPhpVersion = '8.1.0';
$packageName = 'symfony/clock';

if (version_compare(PHP_VERSION, $requiredPhpVersion, '>=')) {
	$tempComposerJson = 'composer.temp.json';
	copy('composer.json', $tempComposerJson);

	putenv("COMPOSER=$tempComposerJson");

	passthru("composer require --dev --no-scripts $packageName");

	unlink($tempComposerJson);
	unlink('composer.temp.lock');
} else {
	echo "Skipping $packageName installation (requires PHP $requiredPhpVersion or higher).\n";
}
