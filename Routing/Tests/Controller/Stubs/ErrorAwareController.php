<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Tests\Controller\Stubs;

use \Selene\Module\Routing\Controller\Controller;

/**
 * @class ErrorAwareController extends BaseController
 * @see BaseController
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ErrorAwareController extends Controller
{
    public function indexAction()
    {
        throw new \Exception('exception thrown in ' . __METHOD__);
    }

    public function onIndexActionError(\Exception $e)
    {
        return 'Caught Exception';
    }
}
