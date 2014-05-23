<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Processor;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Definition\ParentDefinition;
use \Selene\Components\DI\Definition\DefinitionInterface;
use \Selene\Components\DI\Definition\ServiceDefinition;

/**
 * @class ResolveAbstractDefinition implements ProcessInterface
 * @see ProcessInterface
 *
 * @package \Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ResolveParentDefinition implements ProcessInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * process
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return mixed
     */
    public function process(ContainerInterface $container)
    {
        $this->container = $container;

        $this->repeatProcess();
    }

    /**
     * repeatProcess
     *
     * @access private
     * @return void
     */
    private function repeatProcess()
    {
        $filtered = $this->filterDefinitions($this->container->getDefinitions());
        $count = count($filtered);

        if (0 < $count) {
            $this->processDefinitions($filtered);
            $this->repeatProcess();
        }
    }

    /**
     * processDefinitions
     *
     * @param array $definitions
     *
     * @access private
     * @return void
     */
    private function processDefinitions(array $definitions)
    {
        foreach ($definitions as $id => $definition) {
            $this->container->setDefinition($id, $this->replaceDefinition($definition));
        }
    }

    /**
     * replaceDefinition
     *
     * @param \Selene\Components\DI\Definition\ParentDefinition $definition
     * @internal param \Selene\Components\DI\Definition\ParentDefinition $definitions
     *
     * @access private
     * @return DefinitionInterface
     */
    private function replaceDefinition(ParentDefinition $definition)
    {
        $parent = $this->container->getDefinition($definition->getParent());

        $def = new ServiceDefinition;

        $def->merge($parent);

        $def->setParent(null);
        $def->setAbstract(false);

        if ($class = $definition->getClass()) {
            $def->setClass($class);
        }

        if (null !== ($scope = $definition->getScope())) {
            $def->setScope($scope);
        }

        if ($definition->isAbstract()) {
            $def->setAbstract(true);
        }

        if ($definition->isInjected()) {
            $def->setInjected(true);
        }

        if ($definition->requiresFile()) {
            $def->setFile($definition->getFile());
        }

        if ($definition->hasFactory()) {

            $attrs = $definition->getFactory();

            $factoryClass = is_string($attrs) ? $attrs : $attrs[0];
            $method = is_string($attrs) ? null : $attrs[1];

            $def->setFactory($factoryClass, $method);
        }

        if ($definition->hasArguments()) {
            $this->setParentArgs($def, $definition);
        }

        // append metadata:
        if ($definition->hasMetaData()) {
            foreach ($definition->getMetaData() as $data) {
                $def->addMetaData($data->getName(), $data->getParameters());
            }
        }

        // append setters:
        if ($definition->hasSetters()) {
            foreach ($definition->getSetters() as $setter) {
                $method = key($setter);
                $def->addSetter($method, $setter[$method]);
            }
        }

        return $def;
    }

    /**
     * setParentArgs
     *
     * @param ServiceDefinition $definition
     * @param ParentDefinition $parent
     *
     * @access private
     * @return void
     */
    private function setParentArgs(ServiceDefinition $definition, ParentDefinition $parent)
    {
        foreach ($parent->getArguments() as $index => $argument) {
            if (is_string($index) && 0 === strpos($index, 'index_')) {
                $i = (int)substr($index, 6);
                $definition->replaceArgument($argument, $i);
            } elseif (is_int($index)) {
                $definition->addArgument($argument);
            }
        }
    }

    /**
     * filterDefinitions
     *
     * @param array $definitions
     *
     * @access private
     * @return array
     */
    private function filterDefinitions(array $definitions)
    {
        $defs = [];

        foreach ($definitions as $id => $definition) {

            if ($definition->isAbstract()) {
                continue;
            }

            if ($definition instanceof ParentDefinition) {
                $defs[$id] = $definition;
            } elseif ($definition->hasParent()) {
                $defs[$id] = $this->prepareDefinition($definition);
            }
        }

        return $defs;
    }

    /**
     * prepareDefinition
     *
     * @param DefinitionInterface $definition
     *
     * @access private
     * @return ParentDefinition
     */
    private function prepareDefinition(DefinitionInterface $definition)
    {
        return new ParentDefinition($definition->getParent());
    }
}
