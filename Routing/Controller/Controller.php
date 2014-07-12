<?php

/**
 * This File is part of the Selene\Components\Routing\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Controller;

/**
 * @class BaseController
 * @package Selene\Components\Routing\Controller
 * @version $Id$
 */
abstract class Controller
{
    /**
     * callAction
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function callAction($method, array $parameters = [])
    {
        try {
            return call_user_func_array([$this, $method], $parameters);
        } catch (\Exception $e) {

            if (method_exists($this, $handler = $this->getErrorHandlerMethod($method))) {
                return call_user_func_array([$this, $handler], [$e, $parameters]);
            }

            throw $e;
        }
    }

    /**
     * getErrorHandlerMethod
     *
     * @param string $method
     *
     * @return mixed
     */
    protected function getErrorHandlerMethod($method)
    {
        return sprintf('on%sError', ucfirst($method));
    }
}
