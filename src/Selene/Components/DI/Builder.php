<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI;

use \Selene\Components\DI\Dumper\ContainerDumper;

/**
 * @class Builder
 * @package Selene\Components\DI
 * @version $Id$
 */
class Builder
{
    protected $containerClass;

    /**
     * __construct
     *
     * @param Dumper $dumper
     *
     * @access public
     * @return mixed
     */
    public function __construct(ContainerDumper $dumper)
    {
        $this->dumper = $dumper;
    }

    public function setContainerClass($class)
    {
        return $this->containerClass = $class;
    }

    /**
     * getContainerClass
     *
     *
     * @access public
     * @return mixed
     */
    public function getContainerClass()
    {
        return $this->containerClass ?: __NAMESPACE__.'\\BaseContainer';
    }

    /**
     * build
     *
     * @access public
     * @return ContainerInterface
     */
    public function build(callable $postSetup = null)
    {
        if (!class_exists($class = $this->getContainerClass())) {
            throw new \InvalidArgumentException(sprintf('ContainerClass %s does not exist', $class));
        }

        $container = new $class;

        if (null !== $postSetup) {
            call_user_func($postSetup, $container);
        }

        return $container;
    }
}
