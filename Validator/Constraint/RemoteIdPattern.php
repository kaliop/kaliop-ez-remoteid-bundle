<?php

namespace Kaliop\EzRemoteIdBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class RemoteIdPattern extends Constraint
{
    public $invalid = 'kaliop_ez_remote_id.validator.remote_id_pattern.invalid';
    public $invalidDefault = 'kaliop_ez_remote_id.validator.remote_id_pattern.invalid_default';
    public $tooLong = 'kaliop_ez_remote_id.validator.remote_id_pattern.too_long';

    public $contentType = null;
}
