<?php

/**
 * This File is part of the Selene\Components\Config\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Nodes;

/**
 * @class StringNode
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
class StringNode extends ScalarNode
{

    /**
     * regexp
     *
     * @var mixed
     */
    protected $regexp;

    protected $minLen;

    protected $maxLen;

    public function __construct()
    {
        parent::__construct('string');
    }

    /**
     * regexp
     *
     * @param mixed $regexp
     *
     * @access public
     * @return mixed
     */
    public function regexp($regexp)
    {
        $this->regexp = $regexp;
        return $this;
    }

    /**
     * minLength
     *
     * @param mixed $len
     *
     * @access public
     * @return mixed
     */
    public function minLength($len)
    {
        $this->minLen = $len;
        return $this;
    }

    /**
     * maxLength
     *
     * @param mixed $len
     *
     * @access public
     * @return mixed
     */
    public function maxLength($len)
    {
        $this->maxLen = $len;
        return $this;
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

    public function validate($value = null)
    {
        parent::validate($value);

        $this->validateLength($value);
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
     * @access protected
     * @return void
     */
    protected function validateLength($value)
    {
        $len = strlen($value);

        if (is_int($this->maxLen) && is_int($this->minLen)) {
            if (!($len >= $this->minLen && $this->maxLen >= $len)) {
                throw new \OutOfRangeException(
                    sprintf('value lenght must be within the range of %s and %s', $this->minLen, $this->maxLen)
                );
            }
        } elseif (is_int($this->maxLen)) {
            if (!($this->maxLen >= $len )) {
                throw new \LengthException(
                    sprintf('value must not exceed a length of %s', $this->maxLen)
                );
            }
        } elseif (is_int($this->minLen)) {
            if (!($len >= $this->minLen)) {
                throw new \LengthException(
                    sprintf('value must not deceed a length of %s', $this->minLen)
                );
            }
        }
    }

    /**
     * validateRegexp
     *
     * @param mixed $string
     *
     * @access protected
     * @return mixed
     */
    protected function validateRegexp($value)
    {
        if (null === $this->regexp) {
            return true;
        }

        if (!(boolean)preg_match($this->regexp, $value)) {
            throw new \InvalidArgumentException(
                sprintf('value %s doesn\'t macht given pattern', $value)
            );
        }

        return true;
    }
}
