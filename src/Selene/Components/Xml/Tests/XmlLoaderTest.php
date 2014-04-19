<?php

/**
 * This File is part of the Selene\Components\Xml\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Xml\Tests;

use \Selene\Components\Xml\XmlLoader;

/**
 * @class XmlLoaderTest
 * @package Selene\Components\Xml\Tests
 * @version $Id$
 */
class XmlLoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\Xml', new XmlLoader);
    }
}
