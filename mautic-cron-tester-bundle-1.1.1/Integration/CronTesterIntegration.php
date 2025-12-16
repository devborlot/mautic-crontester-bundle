<?php

namespace MauticPlugin\MauticCronTesterBundle\Integration;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Helper\CacheStorageHelper;
use Mautic\CoreBundle\Helper\EncryptionHelper;
use Mautic\CoreBundle\Helper\PathsHelper;
use Mautic\CoreBundle\Model\NotificationModel;
use Mautic\LeadBundle\Model\CompanyModel;
use Mautic\LeadBundle\Model\DoNotContact as DoNotContactModel;
use Mautic\LeadBundle\Model\FieldModel;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Mautic\PluginBundle\Model\IntegrationEntityModel;
use MauticPlugin\MauticCronTesterBundle\Helper\CronTesterHelper;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CronTesterIntegration extends AbstractIntegration
{
    const NAME         = 'CronTester';
    const DISPLAY_NAME = 'Cron tester';

    private $cronTesterHelper;

    public function __construct(EventDispatcherInterface $eventDispatcher,
        CacheStorageHelper $cacheStorageHelper,
        EntityManager $entityManager,
        Session $session,
        RequestStack $requestStack,
        Router $router,
        TranslatorInterface $translator,
        Logger $logger,
        EncryptionHelper $encryptionHelper,
        LeadModel $leadModel,
        CompanyModel $companyModel,
        PathsHelper $pathsHelper,
        NotificationModel $notificationModel,
        FieldModel $fieldModel,
        IntegrationEntityModel $integrationEntityModel,
        DoNotContactModel $doNotContact,
        CronTesterHelper $cronTesterHelper)
    {
        parent::__construct(
            $eventDispatcher,
            $cacheStorageHelper,
            $entityManager,
            $session,
            $requestStack,
            $router,
            $translator,
            $logger,
            $encryptionHelper,
            $leadModel,
            $companyModel,
            $pathsHelper,
            $notificationModel,
            $fieldModel,
            $integrationEntityModel,
            $doNotContact
        );
        $this->cronTesterHelper = $cronTesterHelper;
    }

    public function getName()
    {
        return self::NAME;
    }

    public function getDisplayName()
    {
        return self::DISPLAY_NAME;
    }

    public function getAuthenticationType()
    {
        /* @see \Mautic\PluginBundle\Integration\AbstractIntegration::getAuthenticationType */
        return 'none';
    }

    /**
     * Get icon for Integration.
     *
     * @return string
     */
    public function getIcon()
    {
        return 'plugins/MauticCronTesterBundle/Assets/img/icon.png';
    }

    /**
     * @param \Mautic\PluginBundle\Integration\Form|FormBuilder $builder
     * @param array                                             $data
     * @param string                                            $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        if ('features' == $formArea) {
            $builder->add(
                'pathToMauticConsole',
                TextType::class,
                [
                    'label'       => 'mautic.crontester.form.path_to_mautic_console',
                    'attr'        => [
                        'class' => 'form-control',
                    ],
                    'required'    => true,
                    'data'        => empty($data['pathToMauticConsole']) ? $this->cronTesterHelper->getDefaultConsolePath() : $data['pathToMauticConsole'],
                    'constraints' => [
                        new NotBlank(
                            ['message' => 'mautic.core.value.required']
                        ),
                    ],
                ]
            );
        }
    }
}
