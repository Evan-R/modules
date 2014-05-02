<?php

/**
 * This File is part of the Selene\Components\DI\Dumper\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Dumper\Stubs;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Dumper\Traits\FormatterTrait;

/**
 * @abstract class Stub implements ContainerAwareInterface Stub
 * @see ContainerAwareInterface
 * @abstract
 *
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class Stub implements StubInterface
{
    use FormatterTrait;

    /**
     * @access public
     * @abstract
     * @return string
     */
    abstract public function dump();

    /**
     * __toString
     *
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->dump();
    }
}
