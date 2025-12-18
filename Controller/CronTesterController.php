<?php

declare(strict_types=1);

namespace MauticPlugin\CronTesterBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use MauticPlugin\CronTesterBundle\Helper\CronTesterHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CronTesterController extends CommonController
{
    private ?CronTesterHelper $cronTesterHelper = null;

    /**
     * Dispatch action based on objectAction parameter.
     * CronTesterHelper is injected via method injection by Symfony.
     */
    public function dispatchAction(
        Request $request,
        CronTesterHelper $cronTesterHelper,
        string $objectAction = '',
        string $objectId = '0'
    ): Response {
        $this->cronTesterHelper = $cronTesterHelper;
        $id = (int) $objectId;

        return match ($objectAction) {
            'segmentRebuild'   => $this->segmentRebuildAction($request, $id),
            'campaignRebuild'  => $this->campaignRebuildAction($request, $id),
            'campaignTrigger'  => $this->campaignTriggerAction($request, $id),
            'test'             => new Response('CronTester is working! objectId='.$id),
            default            => $this->notFound(),
        };
    }

    /**
     * Rebuild segment action.
     */
    private function segmentRebuildAction(Request $request, int $objectId): Response
    {
        return $this->processJob($request, 'lead.list', 'segment', $objectId, 'mautic:segments:rebuild', 'list-id');
    }

    /**
     * Campaign rebuild action.
     */
    private function campaignRebuildAction(Request $request, int $objectId): Response
    {
        return $this->processJob($request, 'campaign.campaign', 'campaign', $objectId, 'mautic:campaigns:rebuild', 'campaign-id');
    }

    /**
     * Campaign trigger action.
     */
    private function campaignTriggerAction(Request $request, int $objectId): Response
    {
        return $this->processJob($request, 'campaign.campaign', 'campaign', $objectId, 'mautic:campaigns:trigger', 'campaign-id');
    }

    /**
     * Process the cron job command.
     */
    private function processJob(
        Request $request,
        string $modelName,
        string $routeContext,
        int $objectId,
        string $command,
        string $idOption = 'list-id'
    ): Response {
        $model  = $this->getModel($modelName);
        $entity = $model->getEntity($objectId);

        if (null === $entity) {
            return $this->notFound();
        }

        $returnUrl = $this->generateUrl(
            'mautic_'.$routeContext.'_action',
            ['objectAction' => 'view', 'objectId' => $entity->getId()]
        );

        // Execute command using method-injected helper
        $result = $this->cronTesterHelper->execInBackground($command, $objectId, false, $idOption);

        // Add flash message with result
        if (!empty($result)) {
            $this->addFlashMessage(nl2br(trim($result)));
        } else {
            $this->addFlashMessage('Command executed: '.$command.' --'.$idOption.'='.$objectId);
        }

        return new RedirectResponse($returnUrl);
    }
}
