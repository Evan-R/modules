<?php

/**
 * This File is part of the Selene\Components\TestSuite package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\TestSuite;

use \Mockery as m;

/**
 * Class: TestCase
 *
 * @uses PHPUnit_Framework_TestCase
 * @abstract
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    /**
     * Get a property values of a none public object property.
     *
     * @param string $property
     * @param object $object
     *
     * @access protected
     * @return mixed
     */
    protected function getObjectPropertyValue($property, $object)
    {
        $reflect  = new \ReflectionObject($object);
        $property = $reflect->getProperty($property);

        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * setObjectPropertyValue
     *
     * @param mixed $property
     * @param mixed $object
     *
     * @access protected
     * @return mixed
     */
    protected function setObjectPropertyValue($property, $value, $object)
    {
        $reflect  = new \ReflectionObject($object);
        $property = $reflect->getProperty($property);

        $property->setAccessible(true);

        return $property->setValue($object, $value);
    }

    /**
     * Invoke a none public object method.
     *
     * @param string $method
     * @param object $object
     * @param array $arguments
     *
     * @access protected
     * @return mixed
     */
    protected function invokeObjectMethod($method, $object, array $arguments = [])
    {
        $reflect  = new \ReflectionObject($object);
        $call     = $reflect->getMethod($method);

        $call->setAccessible(true);

        return $call->invokeArgs($object, $arguments);
    }

    /**
     * isWindows
     *
     * @access protected
     * @return boolean
     */
    protected function isWindows()
    {
        return defined('PHP_WINDOWS_VERSION_MAJOR');
    }

    /**
     * markSkippedIfWindows
     *
     * @access protected
     * @return mixed
     */
    protected function markSkippedIfWindows()
    {
        if ($this->isWindows()) {
            $this->markTestSkipped();
        }
    }

    /**
     * giveUp
     *
     * @access protected
     * @return void
     */
    protected function giveUp()
    {
        $this->fail('you lose');
    }
}
