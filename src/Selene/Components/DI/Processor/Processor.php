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

use \Selene\Components\DI\ContainerInterface;

/**
 * @class Processor
 * @package Selene\Components\DI\Processor
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
     * container
     *
     * @var ContainerInterace
     */
    protected $container;

    /**
     * @access public
     */
    public function __construct()
    {
        $this->processes = [];
    }

    /**
     * process
     *
     * @access public
     * @return void
     */
    public function process(ContainerInterface $container)
    {
        $this->container = $container;

        foreach ($this->processes as $group) {
            $this->processGroup($group);
        }
    }

    /**
     * add
     *
     * @param ProcessInterface $process
     * @param mixed $priority
     *
     * @access public
     * @return \Selene\Components\DI\Processor\ProcessorInterface
     */
    public function add(ProcessInterface $process, $priority = ProcessInterface::BEFORE_RESOLVE)
    {
        $this->processes[$priority][] = $process;

        return $this;
    }

    /**
     * processGroup
     *
     * @param array $group
     *
     * @access protected
     * @return void
     */
    protected function processGroup(array $group)
    {
        foreach ($group as $process) {
            $process->process($this->container);
        }
    }
}
