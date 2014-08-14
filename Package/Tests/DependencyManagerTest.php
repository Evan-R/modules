<?php

/**
 * This File is part of the Selene\Module\Package\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package\Tests;

use \Mockery as m;
use \Selene\Module\Package\PackageRepository;
use \Selene\Module\Package\DependencyManager;

/**
 * @class DependencyManagerTest
 * @package Selene\Module\Package\Tests
 * @version $Id$
 */
class DependencyManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldGetDependencies()
    {
        $p10 = m::mock('Selene\Module\Package\PackageInterface');
        $p10->shouldReceive('getName')->andReturn('p10Package')
           ->shouldReceive('getAlias')->andReturn('p10')
           ->shouldReceive('requires')->andReturn([]);

        $p1 = m::mock('Selene\Module\Package\PackageInterface');
        $p1->shouldReceive('getName')->andReturn('p1Package')
           ->shouldReceive('getAlias')->andReturn('p1')
           ->shouldReceive('requires')->andReturn(['p10']);

        $p2 = m::mock('Selene\Module\Package\PackageInterface');
        $p2->shouldReceive('getName')->andReturn('p2Package')
           ->shouldReceive('getAlias')->andReturn('p2')
           ->shouldReceive('requires')->andReturn(['p1', 'p4']);

        $p3 = m::mock('Selene\Module\Package\PackageInterface');
        $p3->shouldReceive('getName')->andReturn('p3Package')
           ->shouldReceive('getAlias')->andReturn('p3')
           ->shouldReceive('requires')->andReturn(['p1']);

        $p4 = m::mock('Selene\Module\Package\PackageInterface');
        $p4->shouldReceive('getName')->andReturn('p4Package')
           ->shouldReceive('getAlias')->andReturn('p4')
           ->shouldReceive('requires')->andReturn(['p1', 'p5']);

        $p5 = m::mock('Selene\Module\Package\PackageInterface');
        $p5->shouldReceive('getName')->andReturn('p5Package')
           ->shouldReceive('getAlias')->andReturn('p5')
           ->shouldReceive('requires')->andReturn(['p1', 'p3', 'optional?']);

        $rep = new PackageRepository();
        $rep->add($p4);
        $rep->add($p5);
        $rep->add($p3);
        $rep->add($p2);
        $rep->add($p1);
        $rep->add($p10);

        $mngr = new DependencyManager($rep);

        $this->assertEquals(['p10', 'p1', 'p3', 'p5', 'p4', 'p2'], array_keys($mngr->getSorted()));

        $this->assertEquals(['p10'], array_keys($mngr->getRequirements($p1)));
        $this->assertEquals(['p10', 'p1'], array_keys($mngr->getRequirements($p1, true)));

        $this->assertEquals(['p10', 'p1', 'p3', 'p5', 'p4'], array_keys($mngr->getRequirements($p2)));
    }

    /** @test */
    public function itShouldDetectCircularRefs()
    {
        //$this->markTestIncomplete();
        $p1 = m::mock('Selene\Module\Package\PackageInterface');
        $p1->shouldReceive('getName')->andReturn('p1Package')
           ->shouldReceive('getAlias')->andReturn('p1')
           ->shouldReceive('requires')->andReturn([]);

        $p2 = m::mock('Selene\Module\Package\PackageInterface');
        $p2->shouldReceive('getName')->andReturn('p2Package')
           ->shouldReceive('getAlias')->andReturn('p2')
           ->shouldReceive('requires')->andReturn(['p1', 'p3']);

        $p3 = m::mock('Selene\Module\Package\PackageInterface');
        $p3->shouldReceive('getName')->andReturn('p3Package')
           ->shouldReceive('getAlias')->andReturn('p3')
           ->shouldReceive('requires')->andReturn(['p2']);

        $rep = new PackageRepository();
        $rep->add($p1);
        $rep->add($p2);
        $rep->add($p3);

        $mngr = new DependencyManager($rep);

        try {
            $r3 = $mngr->getRequirements($p3);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(
                'Circular reference error: Package "p2" requires "p3" wich requires "p2".',
                $e->getMessage()
            );
        }
    }
}
