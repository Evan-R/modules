<?php

/**
 * This File is part of the Selene\Components\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;

/**
 * @class ContainerAwareDispatcher
 * @package Selene\Components\Events
 * @version $Id$
 */
class ContainerAwareDispatcher extends Dispatcher implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function handleContainerException($service)
    {
        if (null === $this->container) {
            throw new \InvalidArgumentException(
                sprintf('Cannot set a service "%s" as handler, no service container is set.', $service)
            );
        }

        parent::handleContainerException($service);
    }
}
