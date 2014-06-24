<?php

/**
 * This File is part of the Selene\Components\Routing\Tests\Controller\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests\Controller\Stubs;

use \Selene\Components\Routing\Controller\Controller as SeleneController;

/**
 * @class Controller
 * @package Selene\Components\Routing\Tests\Controller\Stubs
 * @version $Id$
 */
class Controller extends SeleneController
{
    public function actionIndex($str)
    {
        return $this->render($str);
    }

    public function createUser()
    {
        $id = $this->getRequest()->request->get('user_id');
        return $id;
    }
}
