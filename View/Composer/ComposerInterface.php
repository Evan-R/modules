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

use \Selene\Module\View\RendererInterface;

/**
 * @interface ComposerInterface
 * @package Selene\Module\View\Composer
 * @version $Id$
 */
interface ComposerInterface
{
    public function has($template);

    public function addComposable($template, Composable $composable);

    public function compose(RendererInterface $renderer);
}
