<?php

namespace Kaliop\EzRemoteIdBundle\Validator\Constraint;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LocationRemoteIdValidator extends AbstractRemoteIdValidator
{
    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * LocationRemoteIdValidator constructor.
     * @param LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function isDuplicate($value)
    {
         try {
            $this->locationService->loadLocationByRemoteId($value);

            return true;
        } catch (NotFoundException $e) {
            return false;
        } catch (UnauthorizedException $e) {
            return true;
        }
    }
}
