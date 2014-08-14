<?php

/**
 * This File is part of the Selene\Module\Routing\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Controller;

use \Selene\Module\DI\ContainerInterface;
use \Selene\Module\DI\ContainerAwareInterface;
use \Selene\Module\DI\Traits\ContainerAwareTrait;
use \Selene\Module\DI\Exception\ContainerResolveException;
use \Selene\Module\Routing\Matchers\MatchContext;

/**
 * @class ContainerAwareDispatcher
 * @package Selene\Module\Routing\Controller
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
