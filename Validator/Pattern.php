<?php

namespace Kaliop\EzRemoteIdBundle\Validator;

use Kaliop\EzRemoteIdBundle\Exception\InvalidPatternException;
use Kaliop\EzRemoteIdBundle\Exception\ValueToLongException;

class Pattern
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @var int
     */
    private $maxLength;

    /**
     * @param string $pattern
     */
    public function __construct(array $config)
    {
        $this->pattern = $config['pattern'];
        $this->maxLength = (int)$config['max_length'];
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->pattern;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @param string $value
     * @throws ValueToLongException
     * @throws InvalidPatternException
     */
    public function test(string $value)
    {
        if (strlen($value) > $this->maxLength) {
            throw new ValueToLongException();
        }

        if (!preg_match($this->pattern, $value)) {
            throw new InvalidPatternException();
        }
    }
}
