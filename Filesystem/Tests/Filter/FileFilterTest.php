<?php

/**
 * This File is part of the Selene\Components\Filesystem\Tests\Filter package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem\Tests\Filter;

use \Selene\Components\Filesystem\Filter\FileFilter;

/**
 * @class IgnoreFilterTest extends \PHPUnit_Framework_TestSuite
 * @see \PHPUnit_Framework_TestSuite
 *
 * @package Selene\Components\Filesystem\Tests\Filter
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class FileFilterTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itIsExpectedThat()
    {
        $filter = new FileFilter;

        $filter->add('\.git.*');
        $filter->add('\.svn.*');
        $filter->add('\.sass-.*');

        $this->assertTrue($filter->match('.git'));
        $this->assertTrue($filter->match('/path/.git'));
        $this->assertTrue($filter->match('/tmp/1400147069441/source_tree/.git/index'));
        $this->assertTrue($filter->match('/tmp/1400147069441/source_tree/.gitignore'));
        $this->assertTrue($filter->match('/tmp/1400147069441/source_tree/.sass-cache/file.scss'));
    }
}
