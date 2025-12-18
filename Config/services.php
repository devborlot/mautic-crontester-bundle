<?php

declare(strict_types=1);

use MauticPlugin\CronTesterBundle\Integration\CronTesterIntegration;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->load('MauticPlugin\\CronTesterBundle\\', '../')
        ->exclude('../{Assets,Config,Migrations,Tests,Translations}');

    // Alias for legacy IntegrationHelper compatibility
    // IntegrationHelper::getIntegrationObject() looks for 'mautic.integration.{name}' service
    $services->alias('mautic.integration.crontester', CronTesterIntegration::class)
        ->public();
};
