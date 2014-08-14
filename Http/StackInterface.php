<?php

/**
 * This File is part of the Selene\Module\Net package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Http;

use \Symfony\Component\HttpFoundation\Request;

/**
 * @interface StackInterface
 *
 * @package Selene\Module\Net
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface StackInterface
{
    /**
     * getCurrent
     *
     * @return Request|null
     */
    public function getCurrent();

    /**
     * getPrevious
     *
     * @access public
     * @return mixed
     */
    public function getPrevious();

    /**
     * getMain
     *
     * @return Request
     */
    public function getMain();

    /**
     * removeSubRequests
     *
     * @return void
     */
    public function removeSubRequests();

    /**
     * push
     *
     * @param Request $request
     *
     * @return void
     */
    public function push(Request $request);

    /**
     * pop
     *
     * @return Request
     */
    public function pop();
}
