<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Nodes;

/**
 * @abstract class ScalarNode extends Node
 * @see Node
 * @abstract
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class ScalarNode extends Node
{
    const T_BOOL    = 'boolean';
    const T_FLOAT   = 'float';
    const T_INTEGER = 'integer';
    const T_STRING  = 'string';

    /**
     * type
     *
     * @var string
     */
    protected $type;

    /**
     * Constructor.
     *
     * @param string $type
     */
    public function __construct($type = null)
    {
        if (null !== $type) {
            $this->type = $type;
        }

        $this->setRequired(true);

        parent::__construct();
    }

    public function getType()
    {
        return $this->type;
    }
}
