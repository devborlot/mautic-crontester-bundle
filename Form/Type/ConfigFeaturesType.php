<?php

declare(strict_types=1);

namespace MauticPlugin\CronTesterBundle\Form\Type;

use MauticPlugin\CronTesterBundle\Helper\CronTesterHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConfigFeaturesType extends AbstractType
{
    public function __construct(
        private CronTesterHelper $cronTesterHelper
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'pathToMauticConsole',
            TextType::class,
            [
                'label'       => 'mautic.crontester.form.path_to_mautic_console',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'       => 'form-control',
                    'placeholder' => $this->cronTesterHelper->getDefaultConsolePath(),
                ],
                'required'    => true,
                'data'        => $options['data']['pathToMauticConsole'] ?? $this->cronTesterHelper->getDefaultConsolePath(),
                'constraints' => [
                    new NotBlank(['message' => 'mautic.core.value.required']),
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'integration' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'crontester_config';
    }
}
