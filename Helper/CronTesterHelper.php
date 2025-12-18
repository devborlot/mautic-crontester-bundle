<?php

declare(strict_types=1);

namespace MauticPlugin\CronTesterBundle\Helper;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CronTesterHelper
{
    private string $projectDir;

    public function __construct(
        ParameterBagInterface $parameterBag
    ) {
        $this->projectDir = $parameterBag->get('kernel.project_dir');
    }

    /**
     * Return path to console.
     * For M5, we just use the default path.
     */
    private function getConsolePath(): string
    {
        return $this->getDefaultConsolePath();
    }

    /**
     * Get the default console path.
     */
    public function getDefaultConsolePath(): string
    {
        return $this->projectDir.'/bin/console';
    }

    /**
     * Execute command line in background.
     *
     * @param string $idOption The option name for the ID (e.g., 'list-id' for segments, 'campaign-id' for campaigns)
     */
    public function execInBackground(string $command, ?int $objectId = null, bool $inBackground = false, string $idOption = 'list-id'): string
    {
        $env = $_ENV['APP_ENV'] ?? 'prod';
        $cmd = 'php '.$this->getConsolePath().' '.$command.' --env='.$env;

        if (null !== $objectId) {
            $cmd .= ' --'.$idOption.'='.$objectId;
        }

        @set_time_limit(9999);

        if (false === $inBackground) {
            $output = shell_exec($cmd);

            return $output ?? '';
        }

        if (str_starts_with(php_uname(), 'Windows')) {
            pclose(popen('start /B '.$cmd, 'r'));
        } else {
            exec($cmd.' > /dev/null &');
        }

        return '';
    }
}
