<?php

/**
 * This File is part of the Selene\Components\Kernel\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Events;

use \Symfony\Component\HttpFoundation\Request;

/**
 * @class KernelEvent
 * @package Selene\Components\Kernel\Events
 * @version $Id$
 */
abstract class KernelEvent
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
