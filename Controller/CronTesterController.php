<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      MTCExtendee.com
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCronTesterBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;

class CronTesterController extends FormController
{
    /**
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function segmentRebuildAction($objectId)
    {
        return $this->processJob('lead', 'segment', 'list', $objectId, 'm:s:r');
    }

    /**
     * Campaign rebuild action.
     *
     * @param $objectId
     */
    public function campaignRebuildAction($objectId)
    {
        return $this->processJob('campaign', 'campaign', 'campaign', $objectId, ' m:c:r');
    }

    /**
     * Campaign trigger action.
     *
     * @param $objectId
     */
    public function campaignTriggerAction($objectId)
    {
        return $this->processJob('campaign', 'campaign', 'campaign', $objectId, ' m:c:t');
    }

    /**
     * Process job.
     *
     * @param $bundle
     * @param $entityName
     * @param $objectId
     * @param $command
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function processJob($bundle, $routeContext, $entityName, $objectId, $command)
    {
        $flashes         = [];
        $model           = $this->getModel($bundle.'.'.$entityName);
        $entity          = $model->getEntity($objectId);
        $contentTemplate = 'Mautic'.ucfirst($bundle).'Bundle:'.ucfirst($entityName).':view';
        $activeLink      = '#mautic_'.$routeContext.'_action';
        $mauticContent   = $entityName;
        $returnUrl       = $this->generateUrl(
            'mautic_'.$routeContext.'_action',
            ['objectAction' => 'view', 'objectId' => $entity->getId()]
        );
        $result          = $this->get('mautic.crontester.helper')->execInBackground($command, $objectId);
        if (!empty($result)) {
            $flashes[] = [
                'type'    => 'notice',
                'msg'     => nl2br(trim($result)),
                'msgVars' => [
                    '%name%' => $entity,
                    '%id%'   => $objectId,
                ],
            ];
        }

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => [
                'objectAction' => 'view',
                'objectId'     => $entity->getId(),
            ],
            'contentTemplate' => $contentTemplate,
            'passthroughVars' => [
                'activeLink'    => $activeLink,
                'mauticContent' => $mauticContent,
            ],
        ];

        return $this->postActionRedirect(
            array_merge(
                $postActionVars,
                [
                    'flashes' => $flashes,
                ]
            )
        );
    }
}
