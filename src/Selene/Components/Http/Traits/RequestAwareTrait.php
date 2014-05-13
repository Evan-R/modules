<?php

/**
 * This File is part of the Selene\Components\Http\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Http\Traits;

use \Symfony\Component\HttpFoundation\Request;

/**
 * @class RequestAwareTrait
 * @package Selene\Components\Http\Traits
 * @version $Id$
 */
trait RequestAwareTrait
{
    protected $request;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }
}