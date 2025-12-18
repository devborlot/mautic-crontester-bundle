<?php

declare(strict_types=1);

namespace MauticPlugin\CronTesterBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomButtonEvent;
use Mautic\CoreBundle\Twig\Helper\ButtonHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ButtonSubscriber implements EventSubscriberInterface
{
    private ?CustomButtonEvent $event = null;
    private ?int $objectId = null;

    public function __construct(
        private IntegrationHelper $integrationHelper,
        private TranslatorInterface $translator,
        private RouterInterface $router
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_BUTTONS => ['injectViewButtons', 0],
        ];
    }

    public function injectViewButtons(CustomButtonEvent $event): void
    {
        if (!$this->isPluginEnabled()) {
            return;
        }

        $this->injectCronTesterButtons($event);
    }

    /**
     * Check if the plugin is enabled.
     */
    private function isPluginEnabled(): bool
    {
        $integration = $this->integrationHelper->getIntegrationObject('CronTester');

        // getIntegrationObject can return false or null when not found
        if (false === $integration || null === $integration) {
            return false;
        }

        $settings = $integration->getIntegrationSettings();
        if (null === $settings) {
            return false;
        }

        return $settings->getIsPublished();
    }

    private function injectCronTesterButtons(CustomButtonEvent $event): void
    {
        $objectId = $event->getRequest()->get('objectId');
        $this->setEvent($event);
        $this->setObjectId((int) $objectId);

        $item = $event->getItem();

        if (null !== $item) {
            // Check if this is a segment email (list type)
            if (method_exists($item, 'getEmailType') && 'list' !== $item->getEmailType()) {
                return;
            }

            if (method_exists($item, 'getId')) {
                $this->setObjectId((int) $item->getId());
            }
        }

        $buttons = [
            [
                'objectAction' => 'segmentRebuild',
                'label'        => 'mautic.crontester.rebuild.segment',
                'icon'         => 'fa fa-refresh',
                'context'      => 'segment',
            ],
            [
                'objectAction' => 'campaignRebuild',
                'label'        => 'mautic.crontester.rebuild.campaign',
                'icon'         => 'fa fa-refresh',
                'context'      => 'campaign',
            ],
            [
                'objectAction' => 'campaignTrigger',
                'label'        => 'mautic.crontester.trigger.campaign',
                'icon'         => 'fa fa-play',
                'context'      => 'campaign',
            ],
        ];

        foreach ($buttons as $button) {
            $this->addButtonGenerator(
                $button['objectAction'],
                $button['label'],
                $button['icon'],
                $button['context']
            );
        }
    }

    private function addButtonGenerator(
        string $objectAction,
        string $btnText,
        string $icon,
        string $context,
        int $priority = 1
    ): void {
        $event    = $this->getEvent();
        $objectId = $this->getObjectId();

        if (null === $event || null === $objectId) {
            return;
        }

        $route = $this->router->generate(
            'mautic_cron_tester',
            [
                'objectAction' => $objectAction,
                'objectId'     => $objectId,
            ]
        );

        $button = [
            'attr' => [
                'href'        => $route,
                'data-toggle' => 'ajax',
                'data-method' => 'POST',
            ],
            'btnText'   => $this->translator->trans($btnText),
            'iconClass' => $icon,
            'priority'  => $priority,
        ];

        $event->addButton(
            $button,
            ButtonHelper::LOCATION_PAGE_ACTIONS,
            ['mautic_'.$context.'_action', ['objectAction' => 'view']]
        );
    }

    private function getEvent(): ?CustomButtonEvent
    {
        return $this->event;
    }

    private function setEvent(CustomButtonEvent $event): void
    {
        $this->event = $event;
    }

    private function getObjectId(): ?int
    {
        return $this->objectId;
    }

    private function setObjectId(int $objectId): void
    {
        $this->objectId = $objectId;
    }
}
