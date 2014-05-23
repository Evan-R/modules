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

use \Selene\Components\DI\Container;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;

/**
 * @class Method
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 */
class ServiceMethod extends Stub implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $serviceId;

    /**
     * @param ContainerInterface $container
     *
     * @access public
     * @return mixed
     */
    public function __construct(ContainerInterface $container, $serviceId)
    {
        $this->body = new ServiceBody($container, $serviceId);
        $this->serviceId = $serviceId;
        $this->setContainer($container);
    }

    /**
     * getServiceGetterName
     *
     * @param string $id
     * @param boolean $synced
     * @param boolean $internal
     *
     * @access public
     * @return string
     */
    public static function getServiceGetterName($id, $synced = false, $internal = false)
    {
        $prefix  = sprintf($synced ? 'getSynced%sService' : 'get%sService', $internal ? 'Internal' : '');

        return $prefix.ucfirst(Container::camelCaseStr($id));
    }

    /**
     * dump
     *
     *
     * @access public
     * @return string
     */
    public function dump()
    {
        $body = $this->getMethodBody();
        $id = $this->serviceId;
        $def = $this->container->getDefinition($id);
        $class = ltrim($def->getClass(), '\\');
        $name = static::getServiceGetterName($id, $def->isInjected(), $def->isInternal());

        return <<<EOL
    /**
     * Service: $id
     *
     * @visibility protected
     * @return \\$class
     */
    protected function $name()
    {
        $body
    }

EOL;

    }

    /**
     * getMethodBody
     *
     * @access protected
     * @return mixed
     */
    protected function getMethodBody()
    {
        return $this->body->dump();
    }

    /**
     * dumpMethodDoc
     *
     * @access private
     * @return string
     */
    private function dumpMethodDoc()
    {
        return <<<EOL
    /**
     * Service: $name
     *
     * @visibility $visiblility
     */
EOL;
    }
}
