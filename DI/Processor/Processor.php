<?php

/**
 * This File is part of the Selene\Module\DI\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Processor;

use \Selene\Module\DI\ContainerInterface;

/**
 * @class Processor
 * @package Selene\Module\DI\Processor
 * @version $Id$
 */
class Processor implements ProcessorInterface
{
    /**
     * processes
     *
     * @var array
     */
    protected $processes;

    /**
     * processed
     *
     * @var array
     */
    protected $processed;

    /**
     * container
     *
     * @var ContainerInterface
     */
    protected $container;

    protected $config;

    /**
     * Constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->processes = [];
        $this->processed = [];

        $config->configure($this);
    }

    /**
     * process
     *
     * @param ContainerInterface $container
     *
     * @return boolean false if allready processed
     */
    public function process(ContainerInterface $container)
    {
        if ($this->isProcessed($container)) {
            return false;
        }

        $this->container = $container;

        $this->sort();

        foreach ($this->processes as $group) {
            $this->processGroup($group);
        }

        $this->setProcessed($container);

        return true;
    }

    /**
     * add
     *
     * @param ProcessInterface $process
     * @param mixed $priority
     *
     * @return \Selene\Module\DI\Processor\ProcessorInterface
     */
    public function add(ProcessInterface $process, $priority = ProcessorInterface::BEFORE_OPTIMIZE)
    {
        $this->processes[$priority][] = $process;

        return $this;
    }

    /**
     * processGroup
     *
     * @param array $group
     *
     * @return void
     */
    protected function processGroup(array $group)
    {
        foreach ($group as $process) {
            $process->process($this->container);
        }
    }

    /**
     * isProcessed
     *
     * @param ContainerInterface $container
     *
     * @return boolean
     */
    protected function isProcessed(ContainerInterface $container)
    {
        return in_array(spl_object_hash($container), $this->processed);
    }

    /**
     * setProcessed
     *
     * @param ContainerInterface $container
     *
     * @return void
     */
    protected function setProcessed(ContainerInterface $container)
    {
        $this->processed[] = spl_object_hash($container);
    }

    /**
     * sort
     *
     *
     * @return void
     */
    private function sort()
    {
        ksort($this->processes, SORT_REGULAR);
    }
}
