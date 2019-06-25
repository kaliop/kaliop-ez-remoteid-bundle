<?php


namespace Kaliop\EzRemoteIdBundle\Validator\Constraint;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

abstract class AbstractRemoteIdValidator extends ConstraintValidator
{
    const INVALID_CHARACTER_LIST = [
        '/[:|;]/' => '":", "|", ";"',
        '/\s/' => 'white space'
    ];

    /**
     * @param string $value
     * @param Constraint|LocationRemoteId $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (strlen($value) === 0) {
            return;
        }

        $invalidCharSets = [];
        foreach (self::INVALID_CHARACTER_LIST as $invalidCharRegex => $charDescription) {
            if (preg_match($invalidCharRegex, $value)) {
                $invalidCharSets[] = $charDescription;
            }
        }

        if (!empty($invalidCharSets)) {
            $this->context->buildViolation('kaliop_ez_remote_id.validator.remote_id.invalid_characters', [
                    '%invalidCharacters%' => implode(', ', $invalidCharSets)
            ])->addViolation();
        }


        if ($this->isDuplicate($value)) {
            $this->context->buildViolation($constraint->duplicate)->addViolation();
        }
    }

    /**
     * @param string $value
     * @return boolean
     */
    abstract protected function isDuplicate($value);
}
