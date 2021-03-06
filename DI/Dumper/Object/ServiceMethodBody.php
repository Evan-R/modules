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

use \Selene\Module\Writer\Writer;
use \Selene\Module\Writer\Object\Method;
use \Selene\Module\Writer\FormatterHelper;
use \Selene\Module\Writer\GeneratorInterface;
use \Selene\Module\DI\Reference;
use \Selene\Module\DI\Container;
use \Selene\Module\DI\ContainerInterface;
use \Selene\Module\DI\ContainerAwareInterface;
use \Selene\Module\DI\Traits\ContainerAwareTrait;
use \Selene\Module\DI\Dumper\ImportResolver;
use \Selene\Module\DI\CallerReference;
use \Selene\Module\DI\Definition\DefinitionInterface;

/**
 * @class ServiceMethod
 * @package Selene\Module\DI\Dumper\Object
 * @version $Id$
 */
class ServiceMethodBody implements GeneratorInterface
{
    use FormatterHelper;

    private $writer;
    private $serviceId;
    private $arguments;
    private $container;
    private $classAlias;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param string $id
     * @param string $name
     */
    public function __construct(ContainerInterface $container, $id, $alias = null)
    {
        $this->serviceId = $id;
        $this->container = $container;

        $this->classAlias = $alias;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->generate();
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = false)
    {
        $this->writer = new Writer;

        $this->getServiceInstance();

        return $raw ? $this->writer : $this->writer->dump();
    }

    /**
     * getServiceInstance
     *
     * @return void
     */
    private function getServiceInstance()
    {
        $definition = $this->container->getDefinition($this->serviceId);

        if ($definition->requiresFile()) {
            $this->writer->writeln(sprintf('require_once %s;', $definition->getFile()));
        }

        $parent = $definition->hasParent() ? $this->container->getDefinition($definition->getParent()) : null;

        if ($definition->isInjected()) {
            $instance = null;
        } elseif ($definition->hasFactory()) {
            $instance = $this->getFactoryBody($definition, $parent);
        } else {
            $instance = $this->getServiceBody($definition, $parent);
        }

        if (((bool)$parent && $parent->hasSetters()) || $definition->hasSetters()) {
            $this->writer->writeln(sprintf('$instance = %s;', $this->getInstanceValue($instance)));
            $this->writer->newline();

            $definition = null !== $parent ? $parent : $definition;

            $this->getCallers('$instance', $definition);
            $this->writer->writeln('return $instance;');
        } else {
            $this->writer->writeln($this->getReturnStatement($instance));
        }
    }

    /**
     * getSyncedCallers
     *
     * @param mixed $definition
     *
     * @return void
     */
    protected function getCallers($instance, $definition)
    {
        foreach ($definition->getSetters() as $setters) {

            $synced    = [];
            $method    = key($setters);
            $argsList  = $setters[$method];

            foreach ($argsList as $argument) {
                if ($argument instanceof Reference && $this->container->getDefinition($argument)->isInjected()) {
                    $synced[(string)$argument] = true;
                }
            }

            $args = $this->getArguments($argsList);

            if ((bool)$synced) {
                $this->createSynCallcack($synced, $method, $args);
            } else {
                $this->writer->writeln($this->setServiceArgs($args, $instance.'->'.$method).';');
            }

            $this->writer->newline();
        }
    }

    /**
     * createSynCallcack
     *
     * @param array $synced
     * @param mixed $args
     *
     * @return void
     */
    protected function createSynCallcack(array $synced, $method, array $arguments)
    {
        $this->writer
            ->writeln('$synced = ' . $this->extractParams($synced, 0) . ';')
            ->writeln('$callback = function ($id = null) use (&$synced, $instance) {')
            ->indent()
                ->writeln('unset($synced[$id]);')
                ->newline()
                ->writeln('if (!empty($synced)) {')
                ->indent()
                    ->writeln('return;')
                ->outdent()
                ->writeln('}')
                ->newline()
            ->writeln($this->setServiceArgs($arguments, '$instance->'.$method, 0).';')
            ->outdent()
            ->writeln('};')
            ->newline()
            ->writeln('$this->checkSynced($synced);')
            ->newline()
            ->writeln('if (empty($synced)) {')
            ->indent()
                ->writeln('call_user_func($callback);')
            ->outdent()
            ->writeln('} else {')
                ->indent()
                ->writeln('$this->pushSyncedCallers($synced, $callback);')
            ->outdent()
            ->writeln('}');
    }

    /**
     * getServiceBody
     *
     * @param mixed $definition
     * @param mixed $parent
     *
     * @return string
     */
    protected function getServiceBody($definition, $parent = null)
    {
        if ($parent && !$parent->hasArguments() || !$definition->hasArguments()) {
            return 'new '.$this->getDefinitionClass($definition);
        }

        $args = $this->getServiceArgs($parent ?: $definition);

        return $this->setServiceArgs($args, 'new ' . $this->getDefinitionClass($definition));
    }

    protected function getDefinitionClass(DefinitionInterface $definition)
    {
        if (null !== $this->classAlias) {
            return $this->classAlias;
        }

        return '\\'.ltrim($definition->getClass(), '\\');
    }

    /**
     * getFactoryBody
     *
     * @param mixed $definition
     *
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
     * @return string
     */
    protected function setServiceArgs(array $args, $classCall, $indent = 0)
    {
        $writer = new Writer;

        $writer->writeln($classCall . '(');

        if (empty($args)) {
            $writer->appendln(')');

            return $writer->dump();
        }

        $writer->indent();
        $writer->writeln(implode(",\n", $args));
        $writer->outdent();
        $writer->writeln(')');

        return $writer->dump();

        //return $classCall . '(' . implode(', ', $args). ')';
    }

    /**
     * getServiceArgs
     *
     * @param mixed $definition
     *
     * @return array
     */
    protected function getServiceArgs($definition)
    {
        return $this->getArguments($definition->getArguments());
    }

    /**
     * getArguments
     *
     * @param array $arguments
     *
     * @return array
     */
    protected function getArguments(array $arguments)
    {
        $args = [];

        foreach ($arguments as $key => $argument) {
            if (is_array($argument)) {
                $argument = $this->replaceReferenceInArgsArray($argument);
            }
            if ($argument instanceof Reference) {
                $args[$key] = $this->extractRefenceInstantiator($argument);
            } elseif ($argument instanceof CallerReference) {
                $args[$key] = $this->extractCallerDefinition($argument);
            } elseif (null !== $argument && !is_scalar($argument)) {
                $args[$key] = $this->extractParams($argument, 0);
            } elseif ($this->container->hasParameter($argument)) {
                $args[$key] = '$this->getParameter('.$argument.')';
            } else {
                $args[$key] = $this->exportVar($argument);
            }
        }

        return $args;
    }

    /**
     * extractCallerDefinition
     *
     * @param CallerReference $caller
     *
     * @return string
     */
    protected function extractCallerDefinition(CallerReference $caller)
    {
        $service = $this->extractRefenceInstantiator($caller->getService());

        return sprintf(
            '%s->%s(%s)',
            $service,
            $caller->getMethod(),
            implode(', ', $this->getArguments($caller->getArguments()))
        );
    }

    protected function replaceReferenceInArgsArray(array $arguments)
    {
        $args = [];

        foreach ($arguments as $key => $argument) {
            if (is_array($argument)) {
                $args[$key] = $this->replaceReferenceInArgsArray($argument);
            } elseif ($argument instanceof Reference) {
                $args[$key] = $this->extractRefenceInstantiator($argument);
            } else {
                $args[$key] = $argument;
            }
        }

        return $args;
    }

    protected function getServiceGetterName($id, $synced = false)
    {
        return ucfirst(Container::camelCaseStr($this->serviceId));
    }

    /**
     * extractRefenceInstantiator
     *
     * @param mixed $reference
     *
     * @return string
     */
    protected function extractRefenceInstantiator($reference)
    {
        $definition = $this->container->getDefinition((string)$reference);

        $arguments = $definition->hasArguments();
        $setters = $definition->hasSetters();

        if ($definition->hasParent()) {
            $parent = $this->container->getDefinition($definition->getParent());
            $arguments = $parent->hasArguments();
            $setters = $parent->hasSetters();
        }

        if (!$arguments && !$setters && !$definition->scopeIsContainer()) {
            return 'new '. $this->getDefinitionClass($definition);
        }

        $getter = $definition->isInternal() ? 'getInternal' : 'get';

        return '$this->' . $getter .'(\''. (string)$reference . '\')';
    }

    /**
     * getRetrunStatement
     *
     * @param mixed $content
     *
     * @return string
     */
    private function getReturnStatement($content = null)
    {
        return new ReturnStatement($this->getInstanceValue($content), 0);
    }

    private function getInstanceValue($content = null)
    {
        $definition = $this->container->getDefinition($this->serviceId);

        if ($definition->isInternal()) {
            $value = sprintf('$this->internals[\'%s\'] = %s', $this->serviceId, $content);
        } elseif ($definition->isInjected()) {
            $value = sprintf('$this->getDefault($this->services, \'%s\')', $this->serviceId);
        } elseif ($definition->scopeIsContainer()) {
            $value = sprintf('$this->services[\'%s\'] = %s', $this->serviceId, $content);
        } else {
            $value = sprintf('%s', $content);
        }

        return $value;
    }
}
