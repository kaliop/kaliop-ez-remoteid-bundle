<?php

namespace Kaliop\EzRemoteIdBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class ContentRemoteId extends Constraint
{
    public $duplicate = 'kaliop_ez_remote_id.validator.content_remote_id.duplicate';
}
