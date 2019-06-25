<?php

namespace Kaliop\EzRemoteIdBundle\Form\Type;

use EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentInfoType;
use Kaliop\EzRemoteIdBundle\Validator\Constraint\ContentRemoteId;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContentRemoteIdType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contentInfo', ContentInfoType::class, ['label' => false])
            ->add('remoteId', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new ContentRemoteId()
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save'
            ])
        ;
    }
}
