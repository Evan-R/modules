<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests\Controller\Stubs;

use \Selene\Components\Routing\Controller\BaseController;

/**
 * @class ErrorAwareController extends BaseController
 * @see BaseController
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ErrorAwareController extends BaseController
{
    public function actionIndex()
    {
        throw new \Exception('exception thrown in ' . __METHOD__);
    }

    public function onActionIndexError(\Exception $e)
    {
        return 'Caught Exception';
    }
}
