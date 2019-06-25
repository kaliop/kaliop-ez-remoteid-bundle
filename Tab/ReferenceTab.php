<?php

namespace Kaliop\EzRemoteIdBundle\Tab;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use Kaliop\EzRemoteIdBundle\Form\Type\ContentRemoteIdType;
use Kaliop\EzRemoteIdBundle\Form\Type\LocationRemoteIdType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class ReferenceTab extends AbstractTab
{
    /**
     * @var PermissionResolver
     */
    private $permissionResolver;
    /**
     * @var FormFactory
     */
    private $formFactory;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PermissionResolver $permissionResolver,
        FormFactory $formFactory
    ) {
        parent::__construct($twig, $translator);
        $this->permissionResolver = $permissionResolver;
        $this->formFactory = $formFactory;
    }

    public function getIdentifier(): string
    {
        return 'reference-tab';
    }

    public function getName(): string
    {
        return /** @Desc("Reference") */
            $this->translator->trans('kaliop_ez_remote_id.tab.reference', [], 'kaliop_ez_remote_id');
    }

    /**
     * @param array $parameters
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function renderView(array $parameters): string
    {
        /** @var ContentInfo $contentInfo */
        $contentInfo = $parameters['content']->contentInfo;
        $contentForm = $this->formFactory->createNamed('content_remote_id', ContentRemoteIdType::class, [
            'contentInfo' => $contentInfo,
            'remoteId' => $contentInfo->remoteId
        ]);

        /** @var Location $location */
        $location = $parameters['location'];
        $locationForm = $this->formFactory->createNamed('location_remote_id', LocationRemoteIdType::class, [
            'location' => $location,
            'remoteId' => $location->remoteId
        ]);

        $viewParameters = [
            'can_edit_remote_id' => $this->permissionResolver->hasAccess('kaliop_ez_remote_id', 'edit'),
            'content_form' => $contentForm->createView(),
            'location_form' => $locationForm->createView()
        ];

        return $this->twig->render('KaliopEzRemoteIdBundle:tabs:reference.html.twig', array_merge($viewParameters, $parameters));
    }
}
