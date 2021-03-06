<?php

/**
 * This File is part of the Selene\Module\Writer\Tests\Generator\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Tests\Object;

use \Selene\Module\Writer\Object\Property;

/**
 * @class PropertyTest
 * @package Selene\Module\Writer\Tests\Generator\Object
 * @version $Id$
 */
class PropertyTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldCreateAClassProperty()
    {
        $prop = new Property('foo');

        $expected = <<<EOL
    /**
     * foo
     *
     * @var mixed
     */
    public \$foo;
EOL;
        $this->assertSame($expected, (string)$prop);
    }

    /** @test */
    public function itShouldHaveAnInitialValueAndType()
    {
        $prop = new Property('foo');

        $expected = <<<EOL
    /**
     * foo
     *
     * @var string
     */
    private \$foo = 'foo';
EOL;
        $prop->setValue("'foo'");
        $prop->setType(Property::T_STRING);
        $prop->setVisibility(Property::IS_PRIVATE);
        $this->assertSame($expected, (string)$prop);
    }

    /** @test */
    public function itIsExpectedThat()
    {

        $expected = <<<EOL
    /**
     * Acme\Foo\Wahtever
     *
     * @var mixed
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public \$id;
EOL;
        $prop = new Property('id');

        $prop->setDescription('Acme\Foo\Wahtever');
        $prop->addAnnotation('ORM\Column(type="integer")');
        $prop->addAnnotation('ORM\Id');
        $prop->addAnnotation('ORM\GeneratedValue(strategy="AUTO")');

        $this->assertSame($expected, (string)$prop);
    }
}
