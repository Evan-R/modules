<?php

/**
 * This File is part of the Selene\Components\DI\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Processor;

/**
 * @class Configuration
 * @package Selene\Components\DI\Processor
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Configuration implements ConfigInterface
{
    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $this->getDefaultConfig();
        $this->mergeConfig($config);
    }

    /**
     * configure
     *
     * @param ProcessorInterface $processor
     *
     * @return void
     */
    public function configure(ProcessorInterface $processor)
    {
        foreach ($this->getConfig() as $conf) {
            list ($proc, $prio)  = $conf;
            $processor->add($proc, $prio);
        }
    }

    /**
     * merge
     *
     * @param array $config
     *
     * @return void
     */
    public function mergeConfig(array $config)
    {
        $conf = $this->config;

        $this->setConfig($config);

        $this->config = array_merge($conf, $this->config);
    }

    /**
     * getConfig
     *
     * @return void
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * setConfig
     *
     * @param array $config
     *
     * @return void
     */
    public function setConfig(array $config)
    {
        $this->config = [];

        foreach ($config as $c) {

            list ($process, $priority) = $c;

            $this->addProcess($process, $priority);
        }
    }

    /**
     * addProcess
     *
     * @param ProcessInterface $process
     * @param int $priority
     *
     * @return void
     */
    public function addProcess(ProcessInterface $process, $priority = ProcessorInterface::OPTIMIZE)
    {
        $this->config[] = [$process, $priority];
    }

    /**
     * getDefaultConfig
     *
     * @return array
     */
    protected function getDefaultConfig()
    {
        return [
            [new ResolveParameters, ProcessorInterface::RESOLVE],
            [new ResolveParentDefinition, ProcessorInterface::OPTIMIZE],
            [new ResolveDefinitionDependencies, ProcessorInterface::OPTIMIZE],
            [new ResolveDefinitionArguments, ProcessorInterface::OPTIMIZE],
            [new ResolveCircularReference, ProcessorInterface::OPTIMIZE],
            [new RemoveAbstractDefinition, ProcessorInterface::REMOVE],
            [new ResolveAliasWithDefinition, ProcessorInterface::OPTIMIZE],
            [new ResolveCallerMethodCalls, ProcessorInterface::AFTER_REMOVE]
        ];
    }
}
