<?php

/**
 * This File is part of the Selene\Module\View\Composer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Composer;

use \Selene\Module\DI\ContainerAwareInterface;
use \Selene\Module\DI\Traits\ContainerAwareTrait;

/**
 * @class ContainerAwareComposer
 * @package Selene\Module\View\Composer
 * @version $Id$
 */
class ContainerAwareComposer extends Composer implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * services
     *
     * @var array
     */
    protected $services;

    /**
     * @param array $services
     */
    public function __construct(array $services = [])
    {
        $this->services = $services;
    }

    /**
     * setComposable
     *
     * @param string $template
     * @param string $service
     *
     * @return void
     */
    public function setService($template, $service)
    {
        if (isset($this->composeables[$template])) {
            throw new \InvalidArgumentException(
                sprintf('Can\'t ser composer service for template %s because a componser already exists', $template)
            );
        }
        $this->services[$template] = $service;
    }

    /**
     * hasService
     *
     * @param mixed $template
     *
     * @return boolean
     */
    public function hasService($template)
    {
        return isset($this->services[$template]);
    }

    /**
     * getComposable
     *
     * @param string $template
     *
     * @return Composable
     */
    public function getService($template)
    {
        if (!isset($this->services[$template])) {
            throw new \InvalidArgumentException(sprintf('No service for template %s.', $template));
        }

        return $this->composables[$template] = $this->container->get($this->services[$template]);
    }
}
