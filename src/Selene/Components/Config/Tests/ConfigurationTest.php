<?php

/**
 * This File is part of the Selene\Components\Config\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests;

use Mockery as m;
use Selene\Components\TestSuite\TestCase;
use Selene\Components\Foundation\BundleInterface;
use Selene\Components\Config\Configuration;

/**
 * @class ConfigurationTest
 * @package
 * @version $Id$
 */
class ConfigurationTest extends TestCase
{
    public function testLocateFiles()
    {
        $conf = new Configuration();

        $this->setObjectPropertyValue(
            'configuration',
            [
                '*::app' => ['setting' => []],
                '*::config' => ['setting' => []],
                'FooBundle::config' => ['setting' => ['conf' => 'foo']],
                'FooBundle::routes' => ['setting' => ['home']],
            ],
            $conf
        );

        $val = $conf->get('FooBundle::config.setting');
    }

    public function testAddBundle()
    {
    }
}
