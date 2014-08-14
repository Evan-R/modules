<?php

/**
 * This File is part of the Selene\Module\Routing\Tests\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Tests\Controller;

use \Selene\Module\Routing\Tests\Controller\Stubs\ErrorAwareController;

/**
 * @class BaseControllerTest
 * @package Selene\Module\Routing\Tests\Controller
 * @version $Id$
 */
class BaseControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldHandleExceptions()
    {
        $controller = new ErrorAwareController;

        $this->assertSame('Caught Exception', $controller->callAction('indexAction', []));
    }
}
