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
 * @class ProcessBuilder
 * @package Selene\Module\DI\Processor
 * @version $Id$
 */
class ProcessorDecorator implements ProcessorInterface
{
    /**
     * processor
     *
     * @var ProcessorInterface
     */
    private $processor;

    /**
     * Constructor.
     *
     * @param ProcessorInterface $processor
     */
    public function __construct(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $container)
    {
        throw new \BadMethodCallException('Calling "process()" is not allowed.');
    }

    /**
     * {@inheritdoc}
     *
     * @return \ProcessorInterface this instance.
     */
    public function add(ProcessInterface $process, $priority = ProcessorInterface::BEFORE_OPTIMIZE)
    {
        $this->processor->add($process, $priority);

        return $this;
    }
}
