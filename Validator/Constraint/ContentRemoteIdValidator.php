<?php

namespace Kaliop\EzRemoteIdBundle\Validator\Constraint;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContentRemoteIdValidator extends AbstractRemoteIdValidator
{
    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * LocationRemoteIdValidator constructor.
     * @param ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function isDuplicate($value)
    {
        try {
            $this->contentService->loadContentInfoByRemoteId($value);

            return true;
        } catch (NotFoundException $e) {
            return false;
        } catch (UnauthorizedException $e) {
            return true;
        }
    }
}
