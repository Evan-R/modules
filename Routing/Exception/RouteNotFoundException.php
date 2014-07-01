<?php

/**
 * This File is part of the Selene\Components\Routing\Exception package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Exception;

use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @class RouteNotFoundException
 * @package Selene\Components\Routing\Exception
 * @version $Id$
 */
class RouteNotFoundException extends NotFoundHttpException
{
}
