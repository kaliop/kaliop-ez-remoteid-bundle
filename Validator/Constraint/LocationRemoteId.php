<?php

namespace Kaliop\EzRemoteIdBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class LocationRemoteId extends Constraint
{
    public $duplicate = 'kaliop_ez_remote_id.validator.location_remote_id.duplicate';
}
