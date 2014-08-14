<?php

/**
 * This File is part of the Generator\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Tests\Object;

use \Selene\Module\Writer\Object\InterfaceMethod;

/**
 * @class ArgumentTest
 * @package Generator\Object
 * @version $Id$
 */
class InterfaceMethodTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldThrowExcetptionWhenSetToAbstract()
    {
        $im = new InterfaceMethod('getBar');

        try {
            $im->setAbstract(false);
        } catch (\LogicException $e) {
            $this->fail('setting abstract to false should cause no side effect.');
        }

        try {
            $im->setAbstract(true);
        } catch (\LogicException $e) {
            $this->assertSame('Cannot set interface method abstract.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldThrowExcetptionWhenBodyIsSet()
    {
        $im = new InterfaceMethod('getBar');

        try {
            $im->setBody('return null;');
        } catch (\LogicException $e) {
            $this->assertSame('Cannot set a method body on an interface method.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldWriteInterfaceMethod()
    {
        $expected = <<<EOL
    /**
     * getFoo
     *
     * @return void
     */
    public function getFoo();
EOL;
        $im = new InterfaceMethod('getFoo');

        $this->assertSame($expected, $im->generate());
    }
}
