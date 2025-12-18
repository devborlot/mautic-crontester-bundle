<?php

declare(strict_types=1);

return [
    'name'        => 'Cron Tester',
    'description' => 'Test cron jobs directly from Mautic UI - rebuild segments and trigger campaigns manually',
    'author'      => 'mtcextendee.com (Migrated to M6)',
    'version'     => '6.0.0',

    'routes' => [
        'main' => [
            'mautic_cron_tester' => [
                'path'       => '/cron/tester/{objectAction}/{objectId}',
                'controller' => 'MauticPlugin\CronTesterBundle\Controller\CronTesterController::dispatchAction',
                'defaults'   => [
                    'objectId' => '0',
                ],
            ],
        ],
    ],
];
