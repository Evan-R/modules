<?php

/**
 * This File is part of the Selene\Components\Filesystem\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem\Tests;

use Selene\Components\TestSuite\TestCase;
use Selene\Components\Filesystem\Filter\FileFilter;

/**
 * @class FileFilterTest
 * @package
 * @version $Id$
 */
class FileFilterTest extends TestCase
{

    /**
     * @dataProvider patternProvider
     */
    public function testDetectRegexpPattern($pattern, $isRegexp)
    {
        $filter = new FileFilter((array)$pattern);
        $result = $this->invokeObjectMethod('isRegexp', $filter, [$pattern]);
        $this->assertTrue($result === $isRegexp);
    }

    //public function testRemoveDelimitter()
    //{
    //    $filter = new FileFilter('/(.*\.jpg)/ieii');
    //    //$result = $this->invokeObjectMethod('removeModifyer', $filter, ['/(.*\.jpg)/emxi']);
    //    $result = $this->invokeObjectMethod('removeModifyer', $filter, ['/file']);

    //    //var_dump($result);
    //}

    public function patternProvider()
    {
        return [
            ['.file.*', false],
            ['~.file.*~', true],
            ['/path/to/some_target', false],
            ['path/to/some_target/', false],
            ['~/path/to/some_target~', true],
            ['~*.jpg~', true],
        ];
    }
}
