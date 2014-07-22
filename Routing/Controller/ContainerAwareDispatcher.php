<?php

/**
 * This File is part of the Selene\Components\Routing\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Controller;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;
use \Selene\Components\DI\Exception\ContainerResolveException;
use \Selene\Components\Routing\Matchers\MatchContext;

/**
 * @class ContainerAwareDispatcher
 * @package Selene\Components\Routing\Controller
 * @version $Id$
 */
class ContainerAwareDispatcher extends Dispatcher implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    protected function findController(MatchContext $context)
    {
        list($controller, $action, $callAction) = parent::findController($context);

        if (null !== $this->container && $controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }

        return [$controller, $action, $callAction];
    }
}
