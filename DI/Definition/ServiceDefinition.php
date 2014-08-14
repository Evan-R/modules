<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Definition;

use \Selene\Module\DI\ContainerInterface;

/**
 * @class Definition implements DefinitionInterface
 * @see DefinitionInterface
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ServiceDefinition extends AbstractDefinition
{
    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function setInjected($injected)
    {
        if ((bool)$injected && false === $this->scopeIsContainer()) {
            throw new \LogicException('Cannot inject a service that has not container scope');
        }
        return parent::setInjected($injected);
    }

    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function setScope($scope)
    {
        if ($this->isInjected() && ContainerInterface::SCOPE_PROTOTYPE === $scope) {
            throw new \LogicException('Cannot set prototype scope on an injected service');
        }
        return parent::setScope($scope);
    }
}
