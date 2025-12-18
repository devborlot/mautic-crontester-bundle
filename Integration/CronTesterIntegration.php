<?php

declare(strict_types=1);

namespace MauticPlugin\CronTesterBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\ConfigurationTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CronTesterIntegration extends BasicIntegration implements BasicInterface
{
    use ConfigurationTrait;

    public const NAME         = 'CronTester';
    public const DISPLAY_NAME = 'Cron Tester';

    private string $projectDir;

    public function __construct(
        ParameterBagInterface $parameterBag
    ) {
        $this->projectDir = $parameterBag->get('kernel.project_dir');
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDisplayName(): string
    {
        return self::DISPLAY_NAME;
    }

    public function getIcon(): string
    {
        return 'plugins/CronTesterBundle/Assets/img/icon.png';
    }

    /**
     * Get the default console path for the form.
     */
    public function getDefaultConsolePath(): string
    {
        return $this->projectDir.'/bin/console';
    }
}
