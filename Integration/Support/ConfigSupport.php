<?php

declare(strict_types=1);

namespace MauticPlugin\CronTesterBundle\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\CronTesterBundle\Form\Type\ConfigFeaturesType;
use MauticPlugin\CronTesterBundle\Integration\CronTesterIntegration;

class ConfigSupport extends CronTesterIntegration implements ConfigFormInterface
{
    use DefaultConfigFormTrait;

    public function getConfigFormName(): ?string
    {
        return ConfigFeaturesType::class;
    }
}
