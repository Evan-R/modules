<?php

/**
 * This File is part of the Selene\Components\Net package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Http;

use \Symfony\Component\HttpFoundation\Request;

/**
 * @interface StackInterface
 *
 * @package Selene\Components\Net
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface StackInterface
{
    /**
     * getCurrent
     *
     * @access public
     * @return mixed
     */
    public function getCurrent();

    /**
     * getPrevious
     *
     * @access public
     * @return mixed
     */
    public function getPrevious();


    public function push(Request $request);

    public function pop();
}
