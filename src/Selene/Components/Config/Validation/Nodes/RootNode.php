<?php

/**
 * This File is part of the Selene\Components\Config\Validation\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validation\Nodes;

use \Selene\Components\Config\Validation\Builder;

/**
 * @class RootNode
 * @package Selene\Components\Config\Validation\Nodes
 * @version $Id$
 */
class RootNode extends ArrayNode
{
    /**
     * @param Builder $builder
     *
     * @access public
     */
    public function __construct(Builder $builder, $name = 'root')
    {
        parent::__construct($builder, $name);
    }
}
