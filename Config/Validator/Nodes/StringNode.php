<?php

/**
 * This File is part of the Selene\Module\Config\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Validator\Nodes;

use \Selene\Module\Config\Validator\Exception\ValidationException;

/**
 * @class StringNode
 * @package Selene\Module\Config\Validator\Nodes
 * @version $Id$
 */
class StringNode extends ScalarNode
{
    use Rangeable;

    /**
     * type
     *
     * @var string
     */
    protected $type = self::T_STRING;

    /**
     * regexp
     *
     * @var string|null
     */
    protected $regexp;

    /**
     * regexp
     *
     * @param string $regexp
     *
     * @return StringNode
     */
    public function regexp($regexp)
    {
        $this->regexp = $regexp;

        return $this;
    }

    /**
     * minLength
     *
     * @param int $len
     *
     * @return StringNode
     */
    public function minLength($len)
    {
        return $this->min($len);
    }

    /**
     * maxLength
     *
     * @param mixed $len
     *
     * @return StringNode
     */
    public function maxLength($len)
    {
        return $this->max($len);
    }

    /**
     * lengthBetween
     *
     * @param int $min
     * @param int $max
     *
     * @access public
     * @return StringNode
     */
    public function lengthBetween($min, $max)
    {
        return $this
            ->minLength($min)
            ->maxLength($max);
    }

    /**
     * validateType
     *
     * @param mixed $value
     *
     * @access public
     * @return boolean
     */
    public function validateType($value)
    {
        if (!is_string($value)) {
            return false;
        }

        return true;
    }

    public function validate()
    {
        parent::validate();

        $this->validateLength($value = $this->getValue());
        $this->validateRegexp($value);

        return true;
    }

    /**
     * validateLength
     *
     * @param string $value
     *
     * @throws \OutOfRangeException if both max and min length constraints do not match
     * @throws \LengthException if max or min length constraints do not match
     * @return void
     */
    protected function validateLength($value)
    {
        if (!is_string($value)) {
            return;
        }
        $len = strlen($value);

        $this->checkRange($len);
    }

    /**
     * validateRegexp
     *
     * @param mixed $string
     *
     * @return mixed
     */
    protected function validateRegexp($value)
    {
        if (null === $this->regexp) {
            return;
        }

        if (!(boolean)preg_match($this->regexp, $value)) {
            throw new ValidationException(
                sprintf('%s value "%s" doesn\'t macht given pattern.', $this->getFormattedKey(), $value)
            );
        }

        return true;
    }
}
