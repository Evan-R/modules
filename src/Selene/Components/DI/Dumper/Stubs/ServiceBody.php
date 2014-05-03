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

use \Selene\Components\DI\Reference;
use \Selene\Components\DI\Container;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;

/**
 * @class ServiceBody extends Stub
 * @see Stub
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ServiceBody extends Stub implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container, $serviceId)
    {
        $this->serviceId = $serviceId;
        $this->setContainer($container);
    }

    /**
     * dump
     *
     * @access public
     * @return string
     */
    public function dump()
    {
        return $this->getMethodBody();
    }

    /**
     * getMethodBody
     *
     * @access private
     * @return string
     */
    private function getMethodBody()
    {
        return $this->getServiceInstance();
    }

    /**
     * getServiceInstance
     *
     * @access private
     * @return mixed
     */
    private function getServiceInstance()
    {
        $definition = $this->container->getDefinition($this->serviceId);

        $content = [];

        $returnConstructor = true;

        if ($definition->requiresFile()) {
            $content[] = sprintf('require_once %s;', $definition->getFile());
        }

        $parent = $definition->hasParent() ? $this->container->getDefinition($definition->getParent()) : null;

        if ($definition->isInjected()) {
            $returnConstructor = true;
            $instance = null;
        } elseif ($definition->hasFactory()) {
            $instance = $this->getFactoryBody($definition, $parent);
        } else {
            $instance = $this->getServiceBody($definition, $parent);
        }

        if (((bool)$parent && $parent->hasSetters()) || $definition->hasSetters()) {
            $returnConstructor = false;
            $content[] = sprintf('$instance = %s;', $instance);

            $definition = null !== $parent ? $parent : $definition;

            $this->getCallers('$instance', $definition, $content);
        }

        $content[] = $this->getReturnStatement($returnConstructor ? $instance : '$instance');

        return implode("\n".$this->indent(8), $content);
    }

    /**
     * getSyncedCallers
     *
     * @param mixed $definition
     *
     * @access protected
     * @return string
     */
    protected function getCallers($instance, $definition, &$content)
    {
        foreach ($definition->getSetters() as $setter) {
            $synced    = [];
            $method    = key($setter);
            $arguments = $setter[$method];

            foreach ($arguments as $argument) {
                if ($argument instanceof Reference && $this->container->getDefinition($argument)->isInjected()) {
                    $synced[(string)$argument] = true;
                }
            }

            $args = $this->getArguments($arguments);

            if ((bool)$synced) {
                $content[] = $this->createSynCallcack($synced, $method, $args);
            } else {
                $content[] = $this->setServiceArgs($args, $instance.'->'.$method).';';
            }
        }
    }

    /**
     * createSynCallcack
     *
     * @param array $synced
     * @param mixed $args
     *
     * @access protected
     * @return string
     */
    protected function createSynCallcack(array $synced, $method, array $arguments)
    {
        $lines = (new Lines())
            ->add('$synced = ' . $this->extractParams($synced) . ';')
            ->add('$callback = function ($id = null) use (&$synced, $instance) {')
                ->add('unset($synced[$id]);', 4)
                ->emptyLine()
                ->add('if (!empty($synced)) {')
                    ->add('return;', 4)
                    ->end()
                ->add('}')
                ->emptyLine()
            ->add($this->setServiceArgs($arguments, '$instance->'.$method, 4).';')
            ->end()
            ->add('};')
            ->emptyLine()
            ->add('$this->checkSynced($synced);')
            ->emptyLine()
            ->add('if (empty($synced)) {')
                ->add('call_user_func($callback);', 4)
                ->end()
            ->add('} else {')
                ->add('$this->pushSyncedCallers($synced, $callback);', 4)
            ->end()
            ->add('}')
            ->emptyLine();

        $lines->setOutputIndentation(8);

        return (string)$lines;
    }

    /**
     * getServiceBody
     *
     * @param mixed $definition
     * @param mixed $parent
     *
     * @access protected
     * @return string
     */
    protected function getServiceBody($definition, $parent = null)
    {
        if ($parent && !$parent->hasArguments() || !$definition->hasArguments()) {
            return 'new \\' . ltrim($definition->getClass(), '\\');
        }

        $args = $this->getServiceArgs($parent ?: $definition);

        return $this->setServiceArgs($args, 'new \\' . ltrim($definition->getClass(), '\\'));
    }

    /**
     * getFactoryBody
     *
     * @param mixed $definition
     *
     * @access protected
     * @return string
     */
    protected function getFactoryBody($definition, $parent = null)
    {
        list ($factory, $method) = $definition->getFactory();

        if ($factory instanceof Reference) {
            $caller = '\\' . ServiceMethod::getServiceGetterName($this->serviceId) . '->' . $method;
        } else {
            $reflection = new \ReflectionClass($factory);
            $reflectionMethod = $reflection->getMethod($method);

            if ($reflectionMethod->isStatic()) {
                $caller = '\\' . $reflection->getName() .'::' . $method;
            } else {
                $caller = '(new \\' . $reflection->getName() .')->' . $method;
            }
        }

        $args = $this->getServiceArgs($parent ?: $definition);

        return $this->setServiceArgs($args, $caller);

    }

    protected function getSetters($constructor, $definietion, $parent = null)
    {

    }

    /**
     * setServiceArgs
     *
     * @param array $args
     * @param mixed $classCall
     *
     * @access protected
     * @return string
     */
    protected function setServiceArgs(array $args, $classCall, $indent = 0)
    {
        if (count($args) > 0) {
            $i1 = $this->indent(12 + $indent);
            $i2 = $this->indent(8 + $indent);
            return $classCall . "(\n" . $i1 . implode(",\n".$i1, $args). "\n$i2)";

        }
        return $classCall . '(' . implode(', ', $args). ')';
    }

    /**
     * getServiceArgs
     *
     * @param mixed $definition
     *
     * @access protected
     * @return array
     */
    protected function getServiceArgs($definition)
    {
        return $this->getArguments($definition->getArguments());
    }

    protected function getArguments(array $arguments)
    {
        $args = [];

        foreach ($arguments as $argument) {
            if ($argument instanceof Reference) {
                $synced = $this->container->getDefinition($argument)->isInjected();
                $args[] = '$this->'.ServiceMethod::getServiceGetterName($argument, $synced).'()';
            } elseif (!is_scalar($argument)) {
                $args[] = $this->extractParams($argument, 16);
            } elseif ($this->container->hasParameter($argument)) {
                $args[] = '$this->getParameter('.$argument.')';
            } else {
                $args[] = $this->exportVar($argument);
            }
        }

        return $args;
    }

    protected function getServiceGetterName($id, $synced = false)
    {
        return ucfirst(Container::camelCaseStr($this->serviceId));
    }

    /**
     * getRetrunStatement
     *
     * @param mixed $content
     *
     * @access private
     * @return string
     */
    private function getReturnStatement($content = null)
    {
        $definition = $this->container->getDefinition($this->serviceId);

        if ($definition->isInjected()) {
            $value = sprintf('$this->getDefault($this->services, \'%s\')', $this->serviceId);
        } elseif ($definition->scopeIsContainer()) {
            $value = sprintf('$this->services[\'%s\'] = %s', $this->serviceId, $content);
        } else {
            $value = sprintf('%s', $content);
        }

        return new ReturnStatement($value, 0);
    }
}
