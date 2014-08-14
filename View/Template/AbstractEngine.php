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
 * @class AbstractEngine
 * @package Selene\Module\View\Template
 * @version $Id$
 */
class AbstractEngine implements EngineInterface
{
    protected $extension;

    public function getType()
    {
        return $this->extension;
    }
}
