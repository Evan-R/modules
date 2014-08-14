<?php

/**
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Validator\Nodes;

/**
 * @abstract class ScalarNode extends Node
 * @see Node
 * @abstract
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class ScalarNode extends Node
{

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
        $this->type = null !== $type ? $type : $this->type;

        $this->setRequired(true);

        parent::__construct();
    }
}
