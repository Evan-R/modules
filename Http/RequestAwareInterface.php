<?php

/**
 * This File is part of the Selene\Components\Http package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Http;

use \Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * @class RequestAwareInterface
 * @package Selene\Components\Http
 * @version $Id$
 */
interface RequestAwareInterface
{
    /**
     * setRequest
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return void
     */
    public function setRequest(SymfonyRequest $request);

    /**
     * getRequest
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest();
}
