<?php

namespace Kaliop\EzRemoteIdBundle\Controller;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Kaliop\EzRemoteIdBundle\Form\Type\ContentRemoteIdType;
use Kaliop\EzRemoteIdBundle\Form\Type\LocationRemoteIdType;
use Kaliop\EzRemoteIdBundle\Validator\Constraint\ContentRemoteId;
use Kaliop\EzRemoteIdBundle\Validator\Constraint\LocationRemoteId;
use Kaliop\EzRemoteIdBundle\Validator\Constraint\RemoteIdPattern;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/remoteId")
 */
class RemoteIdController extends AbstractController
{
    /**
     * @var NotificationHandlerInterface
     */
    private $notificationHandler;

    /**
     * RemoteIdController constructor.
     * @param NotificationHandlerInterface $notificationHandler
     */
    public function __construct(NotificationHandlerInterface $notificationHandler)
    {
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @param $locationId
     * @param Request $request
     * @return RedirectResponse
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     * @throws NotFoundException
     * @Route("/location/{locationId}", name="kaliop.remote_id.change_location", methods={"POST"})
     */
    public function changeLocation($locationId, Request $request)
    {
        $router = $this->container->get('router');
        $locationService = $this->getRepository()->getLocationService();
        $location = $locationService->loadLocation($locationId);
        $this->denyAccessUnlessGranted(new Attribute('kaliop_ez_remote_id', 'edit', [
            'valueObject' => $location->getContentInfo()
        ]));

        $form = $this->createNamedForm('location_remote_id', LocationRemoteIdType::class, null, [
            'content_type' => $this->getContentTypeIdentifier($location)
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Location $formLocation */
            $formLocation = $form->get('location')->getData();
            $remoteId = $form->get('remoteId')->getData();


            $updateStruct = $locationService->newLocationUpdateStruct();
            $updateStruct->remoteId = $remoteId;

            $locationService->updateLocation($formLocation, $updateStruct);
        } else {
            $this->notifyFormErrors($form);
        }

        return $this->redirect($router->generate('_ezpublishLocation', [
            'locationId' => $location->id,
            '_fragment' => 'ez-tab-location-view-reference-tab'
        ]) . '#tab');
    }

    /**
     * @param $locationId
     * @param Request $request
     * @return JsonResponse
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @Route("/validate-location/{locationId}", name="kaliop.remote_id.validate_location", methods={"POST"})
     */
    public function validateLocation($locationId, Request $request)
    {
        $locationService = $this->getRepository()->getLocationService();
        $location = $locationService->loadLocation($locationId);
        $this->denyAccessUnlessGranted(new Attribute('kaliop_ez_remote_id', 'edit', [
            'valueObject' => $location->getContentInfo()
        ]));
        $remoteId = $request->request->get('remoteId', '');

        $validator = $this->getValidator();
        $violationList = $validator->validate($remoteId, [
            new NotBlank(),
            new LocationRemoteId(),
            new RemoteIdPattern([
                'contentType' => $this->getContentTypeIdentifier($location)
            ])
        ]);

        return $this->violationListToResponse($violationList);
    }

    /**
     * @param $locationId
     * @param Request $request
     * @return JsonResponse
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @Route("/validate-content/{locationId}", name="kaliop.remote_id.validate_content", methods={"POST"})
     */
    public function validateContent($locationId, Request $request)
    {
        $locationService = $this->getRepository()->getLocationService();
        $location = $locationService->loadLocation($locationId);
        $this->denyAccessUnlessGranted(new Attribute('kaliop_ez_remote_id', 'edit', [
            'valueObject' => $location->getContentInfo()
        ]));
        $remoteId = $request->request->get('remoteId', '');

        $validator = $this->getValidator();
        $violationList = $validator->validate($remoteId, [
            new NotBlank(),
            new ContentRemoteId(),
            new RemoteIdPattern([
                'contentType' => $this->getContentTypeIdentifier($location)
            ])
        ]);

        return $this->violationListToResponse($violationList);
    }

    /**
     * @param $locationId
     * @param Request $request
     * @return RedirectResponse
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     * @throws NotFoundException
     * @Route("/content/{locationId}", name="kaliop.remote_id.change_content", methods={"POST"})
     */
    public function changeContent($locationId, Request $request)
    {
        $router = $this->container->get('router');
        $locationService = $this->getRepository()->getLocationService();
        $location = $locationService->loadLocation($locationId);
        $this->denyAccessUnlessGranted(new Attribute('kaliop_ez_remote_id', 'edit', [
            'valueObject' => $location->getContentInfo()
        ]));

        $form = $this->createNamedForm('content_remote_id', ContentRemoteIdType::class, null, [
            'content_type' => $this->getContentTypeIdentifier($location)
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ContentInfo $location */
            $contentInfo = $form->get('contentInfo')->getData();
            $remoteId = $form->get('remoteId')->getData();

            $contentService = $this->getRepository()->getContentService();
            $updateStruct = $contentService->newContentMetadataUpdateStruct();
            $updateStruct->remoteId = $remoteId;

            $contentService->updateContentMetadata($contentInfo, $updateStruct);
        } else {
            $this->notifyFormErrors($form);
        }

        return $this->redirect($router->generate('_ezpublishLocation', [
            'locationId' => $location->id,
            '_fragment' => 'ez-tab-location-view-reference-tab'
        ]) . '#tab');
    }

    private function violationListToArray(ConstraintViolationListInterface $violationList)
    {
        if ($violationList->count() === 0) {
            return [];
        }

         $errors = [];
        /** @var ConstraintViolation $violation */
        foreach ($violationList as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }

    /**
     * @param ConstraintViolationListInterface $violationList
     * @return JsonResponse
     */
    private function violationListToResponse(ConstraintViolationListInterface $violationList)
    {
        if ($violationList->count() === 0) {
            return new JsonResponse([
                'valid' => true
            ]);
        }

        $errors = $this->violationListToArray($violationList);

        return new JsonResponse([
            'valid' => false,
            'errors' => $errors
        ]);
    }

    /**
     * @return Repository
     */
    private function getRepository()
    {
        return $this->container->get('ezpublish.api.repository');
    }

    /**
     * @param string $name
     * @param mixed|null $type
     * @param null $data
     * @param array $options
     * @return FormInterface
     */
    private function createNamedForm($name, $type, $data = null, $options = [])
    {
        /** @var FormFactory $formFactory */
        $formFactory = $this->container->get('form.factory');

        return $formFactory->createNamed($name, $type, $data, $options);
    }

    /**
     * @return ValidatorInterface
     */
    private function getValidator()
    {
        return $this->container->get('validator');
    }

    /**
     * @param Location $location
     * @return string
     */
    private function getContentTypeIdentifier(Location $location)
    {
        $contentTypeRepository = $this->getRepository()->getContentTypeService();

        try {
            $contentType = $contentTypeRepository->loadContentType($location->contentInfo->contentTypeId);
        } catch (NotFoundException $exception) {
            return null;
        }

        return $contentType->identifier;
    }

    /**
     * @param FormInterface $form
     */
    private function notifyFormErrors(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        $this->notificationHandler->error(implode(" ", $errors));
    }
}
