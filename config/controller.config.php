<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 31/03/2016
 * Time: 4:28 PM
 */

return [
    'initializers' => [
        'AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setConsumer($locator->get('Consumer'));
            }
        },

        'AlthingiAggregator\Lib\Provider\ProviderAwareInterface' => function ($instance, $sm) {
            if ($instance instanceof \AlthingiAggregator\Lib\Provider\ProviderAwareInterface) {
                $locator = $sm->getServiceLocator();
                $instance->setProvider($locator->get('Provider'));
            }
        }
    ]
];
