<?php

/**
 * This File is part of the Selene\Module\View\Template package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Template;

/**
 * @interface EngineResolverInterface
 * @package Selene\Module\View\Template
 * @version $Id$
 */
interface EngineResolverInterface
{
    public function resolve($engine);

    public function registerEngine(EngineInterface $engine);

    public function register($engine, callable $resolver);

}

