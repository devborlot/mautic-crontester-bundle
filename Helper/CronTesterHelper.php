<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      MTCExtendee.com
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCronTesterBundle\Helper;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;

class CronTesterHelper
{
    private $integrationHelper;
    private $projectDir;
    /**
     * CronTesterHelper constructor.
     *
     * @param string $projectDir
     */
    public function __construct(CoreParametersHelper $coreParametersHelper, IntegrationHelper $integrationHelper, $projectDir)
    {
        $this->integrationHelper    = $integrationHelper;
        $this->projectDir           = $projectDir;
    }
    /**
     * Return path to console.
     *
     * @return string
     */
    private function getConsolePath()
    {
        $features = $this->integrationHelper->getIntegrationObject('CronTester')->mergeConfigToFeatureSettings();
        if (!empty($features['pathToMauticConsole'])) {
            return $features['pathToMauticConsole'];
        }

        return $this->getDefaultConsolePath();
    }
    /**
     * @return string
     */
    public function getDefaultConsolePath()
    {
        return $this->projectDir.'/bin/console';
    }
    /**
     * Execute command line in background.
     *
     * @param      $command
     * @param      $objectId
     * @param bool $inBackground
     *
     * @return int|string
     */
    public function execInBackground($command, $objectId = null, $inBackground = false)
    {
        $cmd = 'php '.$this->getConsolePath().' '.$command.' --env='.MAUTIC_ENV;
        if ($objectId) {
            $cmd .= ' -i '.$objectId;
        }
        @set_time_limit(9999);
        if (false === $inBackground) {
            return shell_exec($cmd);
        } else {
            if ('Windows' == substr(php_uname(), 0, 7)) {
                return pclose(popen('start /B '.$cmd, 'r'));
            } else {
                return exec($cmd.' > /dev/null &');
            }
        }
    }
}
