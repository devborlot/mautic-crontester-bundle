<?php

return [
    'name'        => 'MauticCronTesterBundle',
    'description' => 'Extend your Mautic with awesome features',
    'author'      => 'mtcextendee.com',
    'version'     => '1.0.1',
    'routes'      => [
        'main' => [
            'mautic_cron_tester' => [
                'path'       => '/cron/tester/{objectAction}/{objectId}',
                'controller' => 'MauticCronTesterBundle:CronTester:execute',
            ],
        ],
    ],
    'services'    => [
        'events' => [
            'mautic.crontester.button.subscriber' => [
                'class'     => \MauticPlugin\MauticCronTesterBundle\EventListener\ButtonSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'translator',
                    'router',
                ],
            ],
        ],
        'others' => [
            'mautic.crontester.helper' => [
                'class'     => \MauticPlugin\MauticCronTesterBundle\Helper\CronTesterHelper::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.helper.integration',
                    '%kernel.project_dir%',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.crontester' => [
                'class'     => \MauticPlugin\MauticCronTesterBundle\Integration\CronTesterIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'logger',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                    'mautic.crontester.helper',
                ],
            ],
        ],
    ],
];
