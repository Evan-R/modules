<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Tests\Mapper\Stubs;

use \Symfony\Component\HttpFoundation\Request;

/**
 * @class ControllerStub
 * @package Selene\Module\Routing
 * @version $Id$
 */
class ControllerStub
{
    public function __construct(\PHPUnit_Framework_TestCase $testCase = null)
    {
        $this->testCase = $testCase;
    }

    public function indexAction($foo, $bar, $baz)
    {
        var_dump(func_get_args());
    }

    public function requestAction(Request $request)
    {
        $this->assertTrue(true);
    }

    protected function fooAction()
    {

    }
}
