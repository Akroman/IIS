<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;
use Nette\Forms\Container;
use Nextras\Forms\Controls;
use Nextras\FormComponents;

require_once __DIR__ . '/Constants/DatabaseConstants.php';
require_once __DIR__ . '/Constants/Paths.php';


class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;

		//$configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP
		$configurator->enableTracy(__DIR__ . '/../log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig(__DIR__ . '/config/common.neon');
		$configurator->addConfig(__DIR__ . '/config/local.neon');

        $container = $configurator->createContainer();

        Container::extensionMethod('addOptionList', function (Container $container, $name, $label = NULL, array $items = NULL) {
            return $container[$name] = new Controls\OptionList($label, $items);
        });
        Container::extensionMethod('addMultiOptionList', function (Container $container, $name, $label = NULL, array $items = NULL) {
            return $container[$name] = new Controls\MultiOptionList($label, $items);
        });
        Container::extensionMethod('addDatePicker', function (Container $container, $name, $label = NULL) {
            return $container[$name] = new FormComponents\Controls\DateControl($label);
        });
        Container::extensionMethod('addDateTimePicker', function (Container $container, $name, $label = NULL) {
            return $container[$name] = new FormComponents\Controls\DateTimeControl($label);
        });
        Container::extensionMethod('addTypeahead', function(Container $container, $name, $label = NULL, $callback = NULL) {
            return $container[$name] = new Controls\Typeahead($label, $callback);
        });

        \Kdyby\Replicator\Container::register();

		return $configurator;
	}
}
