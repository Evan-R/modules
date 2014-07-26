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

use \Selene\Components\Config\Validator\Exception\ValidationException;

/**
 * @class EnumNode
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
class EnumNode extends ScalarNode
{
    /**
     * defaultValues
     *
     * @var array
     */
    protected $defaultValues;

    /**
     * values
     *
     * @param array $values
     *
     * @access public
     * @return NodeInterface
     */
    public function values()
    {
        $this->defaultValues = func_get_args();
        return $this;
    }

    /**
     * validateType
     *
     * @param mixed $type
     *
     * @access public
     * @return boolean
     */
    public function validateType($type)
    {
        return is_scalar($type);
    }

    /**
     * validate
     *
     * @param mixed $value
     *
     * @access public
     * @return boolean
     */
    public function validate()
    {
        parent::validate();

        $value = $this->getValue();

        if (!in_array($value, $def = (array)$this->defaultValues)) {
            throw new ValidationException(
                sprintf('allowed values: "%s", but value "%s" given', implode('", "', $def), $value)
            );
        }

        return true;
    }
}
