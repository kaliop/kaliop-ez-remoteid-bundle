<?php

namespace Kaliop\EzRemoteIdBundle\Validator\Constraint;

use Kaliop\EzRemoteIdBundle\Exception\InvalidPatternException;
use Kaliop\EzRemoteIdBundle\Exception\ValueToLongException;
use Kaliop\EzRemoteIdBundle\Validator\Pattern;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RemoteIdPatternValidator extends ConstraintValidator
{
    const TRANS_DOMAIN = 'kaliop_ez_remote_id';

    /**
     * @var Pattern
     */
    private $defaultPattern;

    /**
     * @var Pattern[]
     */
    private $contentTypePatterns;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * RemoteIdPatternValidator constructor.
     * @param array $defaultPattern
     * @param array[] $contentTypePatterns ['pattern' => string, 'max_length' => int]
     */
    public function __construct(array $defaultPattern, array $contentTypePatterns, TranslatorInterface $translator)
    {
        $this->defaultPattern = new Pattern($defaultPattern);
        foreach ($contentTypePatterns as $contentType => $patternConfig) {
            $this->contentTypePatterns[$contentType] = new Pattern($patternConfig);
        }
        $this->translator = $translator;
    }

    /**
     * @param string $value
     * @param Constraint|RemoteIdPattern $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        $contentType = $constraint->contentType;
        $hasContenTypePattern = $contentType && isset($this->contentTypePatterns[$contentType]);
        $pattern = $hasContenTypePattern ? $this->contentTypePatterns[$contentType] : $this->defaultPattern;

        try {
            $pattern->test($value);
        } catch (ValueToLongException $exception) {
            $this->context->buildViolation($constraint->tooLong)
                ->setParameter('%maxLength%', $pattern->getMaxLength())
                ->addViolation();

            return;
        } catch (InvalidPatternException $exception) {
            $this->addInvalidPatternViolation($constraint, $pattern);

            return;
        }
    }

    /**
     * @param Pattern $pattern
     * @return string
     */
    private function addInvalidPatternViolation(RemoteIdPattern $constraint, Pattern $pattern)
    {
        $transKey = sprintf('pattern_description.%s', $pattern->toString());
        $description = $this->translator->trans($transKey, [
            '%pattern' => $pattern->toString()
        ], self::TRANS_DOMAIN);

        if ($transKey !== $description) {
            $this->context->buildViolation($constraint->invalid)
                ->setParameter('%patternDescription%', $description)
                ->addViolation();
        } else {
            $this->context->buildViolation($constraint->invalidDefault)
                ->setParameter('%pattern%', $pattern->toString())
                ->addViolation();
        }
    }
}
