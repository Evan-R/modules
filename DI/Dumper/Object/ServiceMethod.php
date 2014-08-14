<?php

/**
 * This File is part of the Selene\Module\DI\Dumper\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Dumper\Object;

use \Selene\Module\Writer\Object\Method;
use \Selene\Module\Writer\Object\Argument;
use \Selene\Module\Writer\FormatterHelper;
use \Selene\Module\DI\Container;
use \Selene\Module\DI\ContainerInterface;

/**
 * @class ServiceMethod
 * @package Selene\Module\DI\Dumper\Object
 * @version $Id$
 */
class ServiceMethod extends Method
{
    use FormatterHelper;

    /**
     * id
     *
     * @var string
     */
    private $id;

    protected $body;

    /**
     * container
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param string $id
     */
    public function __construct(ContainerInterface $container, $id)
    {
        $this->id = $id;
        $this->container = $container;

        parent::__construct($this->getName(), Method::IS_PROTECTED);
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = false)
    {
        $this->setType($this->getServiceClass());

        return parent::generate($raw);
    }

    /**
     * {@inheritdoc}
     */
    public function addArgument(Argument $argument)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setAbstract($abstract)
    {
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
     * getName
     *
     * @return string
     */
    public function getName()
    {
        $def = $this->container->getDefinition($this->id);

        return static::getServiceGetterName($this->id, $def->isInjected(), $def->isInternal());
    }

    /**
     * getServiceClass
     *
     * @return string
     */
    protected function getServiceClass()
    {
        return $this->container->getDefinition($this->id)->getClass();
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
    /**
     * getBody
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body ?: new ServiceMethodBody($this->container, $this->id);
    }

    /**
     * getDocBlock
     *
     * @return string
     */
    protected function getDocBlock()
    {
        $this->setDocComment('Service: ' . $this->id);

        return parent::getDocBlock();
    }
}
