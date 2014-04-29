<?php

/**
 * This File is part of the Selene\Components\Xml\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Xml\Tests\Loader;

use \Selene\Components\Xml\Loader\Loader;

/**
 * @class XmlLoaderTest
 * @package Selene\Components\Xml\Tests
 * @version $Id$
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\Xml\Loader\LoaderInterface', new Loader);
    }
}
