<?php

/**
 * This File is part of the Selene\Module\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests;

use \Mockery as m;
use \Selene\Module\TestSuite\TestCase;
use \Selene\Module\DI\Parameters;

/**
 * @class ParametersTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Module\DI\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ParametersTest extends TestCase
{

    protected function trearDown()
    {
        m::close();
        parent::tearDown();
    }

    protected function getParameters(array $params = [])
    {
        return new Parameters($params);
    }

    /**
     * @test
     */
    public function testConstructParameters()
    {
        $parameters = $this->getParameters($params = ['foo' => 'bar', 'fuzz' => 'ball']);

        $this->assertEquals($params, $parameters->all(), '->all() should return all given parameters as array');

        $parameters = $this->getParameters($params = ['FOO' => 'bar', 'FUZZ' => 'ball']);

        $this->assertEquals(
            array_change_key_case($params),
            $parameters->all(),
            '->all() should return all given parameters as array where parameter keys are converted to lowercase'
        );
    }

    /** @test */
    public function itShouldHaveArrayAccess()
    {
        $parameters = $this->getParameters($params = ['foo' => 'bar', 'fuzz' => 'ball']);

        $this->assertSame('bar', $parameters['foo']);
        unset($parameters['foo']);

        $this->assertFalse($parameters->has('foo'));

        $parameters['foo'] = 'baz';

        $this->assertSame('baz', $parameters['foo']);
        $this->assertTrue(isset($parameters['foo']));
    }

    /**
     * @test
     */
    public function testGetAndSet()
    {
        $parameters = $this->getParameters($params = ['foo' => 'bar']);
        $parameters->set('fuzz', 'ball');

        $this->assertSame('ball', $parameters->get('fuzz'));
        $this->assertSame('ball', $parameters->get('Fuzz'));
        $this->assertSame('bar', $parameters->get('foo'));
        $this->assertSame('bar', $parameters->get('Foo'));

        $parameters->set('foo', 'nonfoo');
        $this->assertSame('nonfoo', $parameters->get('Foo'));

    }

    /** @test */
    public function itShouldThrowExceptionIfTryingToGetNonDefinedParameter()
    {
        $parameters = $this->getParameters($params = ['foo' => 'bar']);

        try {
            $parameters->get('baz');
        } catch (\Selene\Module\DI\Exception\ParameterNotFoundException $e) {
            $this->assertSame('Parameter \'baz\' was not found', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /** @test */
    public function itShouldResolveObjects()
    {
        $parameters = $this->getParameters($params = ['foo' => new \StdClass]);

        $params = $parameters->resolve()->all();

        $this->assertInstanceof('stdClass', $params['foo']);
    }

    /**
     * @test
     */
    public function testResolveString()
    {
        $parameters = $this->getParameters($params = ['foo' => 'bar', 'note' => 'this is a note that sais %foo%']);

        $this->assertEquals('bam', $parameters->resolveString('bam'));

        $this->assertEquals('this is bar', $parameters->resolveString('this is %foo%'));
        $this->assertEquals('note: this is a note that sais bar', $parameters->resolveString('note: %note%'));
    }

    /**
     * @test
     */
    public function testResolveParam()
    {
        $parameters = $this->getParameters($params = ['foo' => 'bar']);
        //$this->assertEquals('bam', $parameters->resolveParam('bam'));

        $this->assertEquals(['bar' => 'bar'], $parameters->resolveParam(['%foo%' => '%foo%']));

        $parameters->set('object', $obj = m::mock('\Foo\Bar'));

        $this->assertSame($obj, $parameters->resolveParam('%object%'));

        $parameters = $this->getParameters($params = ['foo' => true]);
        $this->assertTrue($parameters->resolveParam('%foo%'));

        try {
            $parameters = $this->getParameters(['foo' => '%bar%', 'bar' => '%foobar%', 'foobar' => '%foo%']);
            $parameters->resolveParam('%foo%');
            $this->fail('->resolve() has failed');
        } catch (\Selene\Module\DI\Exception\ParameterResolvingException $e) {
            $this->assertEquals('[circular reference]: param "foo" is referencing itself', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail('->resolve() should detect circular references');
        }
    }

    /**
     * @test
     */
    public function testTest()
    {
        $params = ['foo' => 'bar', 'bar' => 'fuzz', 'foo.params' => ['%foo%', '%bar%'], 'foo.class' => 'Acme\FooClass'];
        $parameters = $this->getParameters($params);

        $parameters->resolve();
        $this->assertEquals(['bar', 'fuzz'], $parameters->get('foo.params'), 'Parameters should be resolved to ["bar", "fuzz"]');
    }

    /**
     * @test
     */
    public function testResolve()
    {
        $parameters = $this->getParameters($params = ['foo' => 'bar %bar% %foo%', 'bar' => 'bam']);

        try {
            $parameters->resolve();
        } catch (\Selene\Module\DI\Exception\ParameterResolvingException $e) {
            $this->assertEquals('[circular reference]: param "foo" is referencing itself', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail('->resolve() should detect circular references');
        }

        $params = [
            'foo' => 'bar',
            'nest' => [
                'param' => '%nest%'
            ]
        ];

        $parameters = $this->getParameters($params);

        try {
            $parameters->resolve();
            $this->fail('->resolve() has failed');
        } catch (\Selene\Module\DI\Exception\ParameterResolvingException $e) {
            $this->assertEquals('[circular reference]: param "nest" is referencing itself', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail('->resolve() should detect circular references');
        }

        $parameters = $this->getParameters($params = ['foo' => 'bar', 'bar' => '%foo%']);
        $parameters->resolve();

        $this->assertEquals(['foo' => 'bar', 'bar' => 'bar'], $parameters->all());
    }

    /**
     * @test
     */
    public function resolveShouldResetAfterSettingNewParam()
    {
        $parameters = $this->getParameters($params = ['foo' => 'bar', 'bar' => 'bam']);
        $parameters->resolve();
        $this->assertTrue($parameters->isResolved());

        $parameters->set('fuzz', 'ball');
        $this->assertFalse($parameters->isResolved(), '->set() should reset the collection to an unresolved state.');

        $parameters = $this->getParameters($params = ['foo' => 'bar', 'bar' => 'bam']);
        $parameters->resolve();
        $parameters->replaceParams(['fuzz', 'ball']);

        $this->assertFalse($parameters->isResolved(), '->replaceParams() should reset the collection to an unresolved state.');
    }

    /**
     * @test
     */
    public function testMergeParameters()
    {
        $parametersA = $this->getParameters($paramsA = ['foo' => 'bar', 'bar' => '%foo%']);
        $parametersB = $this->getParameters($paramsA = ['fuzz' => 'ball']);

        $parametersA->resolve();

        $parametersA->merge($parametersB);

        $this->assertFalse($parametersA->isResolved());

        $parametersA = $this->getParameters($paramsA = ['foo' => 'bar', 'bar' => '%foo%']);
        $parametersB = $this->getParameters($paramsA = ['fuzz' => 'ball']);

        $parametersA->resolve();
        $parametersB->resolve();

        $parametersA->merge($parametersB);

        $this->assertTrue($parametersA->isResolved());

        $this->assertTrue($parametersA->has('fuzz'));


        try {
            $parametersA->merge($parametersA);
        } catch (\LogicException $e) {
            $this->assertSame(sprintf('%s: cannot merge same instance', get_class($parametersA)), $e->getMessage());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /** @test */
    public function itShouldRemoveParameters()
    {
        $parameters = $this->getParameters($paramsA = ['foo' => 'bar']);

        $this->assertTrue($parameters->has('foo'));

        $parameters->remove('foo');

        $this->assertFalse($parameters->has('foo'));
    }

    /** @test */
    public function itShouldEscapeValues()
    {
        $parameters = $this->getParameters();

        $parameters->set('foo', $parameters->escape(array('bar' => array('ding' => 'I\'m a bar %foo %bar', 'zero' => null))));
        $parameters->set('bar', $parameters->escape('I\'m a %foo%'));

        $this->assertEquals('I\'m a %%foo%%', $parameters->get('bar'), '->escapeValue() escapes % by doubling it');
        $this->assertEquals(array('bar' => array('ding' => 'I\'m a bar %%foo %%bar', 'zero' => null)), $parameters->get('foo'), '->escapeValue() escapes % by doubling it');
    }

    /** @test */
    public function itSouldUnEscapeStringsInArrays()
    {
        $parameters = $this->getParameters();

        $data = $parameters->unescape([
            'foo' => '%%bar%%'
        ]);

        $this->assertSame(['foo' => '%bar%'], $data);
    }

    ///**
    // * @covers Symfony\Component\DependencyInjection\ParameterBag\ParameterBag::resolve
    // * @dataProvider stringsWithSpacesProvider
    // */
    //public function testResolveStringWithSpacesReturnsString($expected, $test, $description)
    //{
    //    $bag = $this->getParameters(array('foo' => 'bar'));

    //    try {
    //        $this->assertEquals($expected, $bag->resolveString($test), $description);
    //    } catch (ParameterNotFoundException $e) {
    //        $this->fail(sprintf('%s - "%s"', $description, $expected));
    //    }
    //}

    //public function stringsWithSpacesProvider()
    //{
    //    return array(
    //        array('bar', '%foo%', 'Parameters must be wrapped by %.'),
    //        array('% foo %', '% foo %', 'Parameters should not have spaces.'),
    //        array('{% set my_template = "foo" %}', '{% set my_template = "foo" %}', 'Twig-like strings are not parameters.'),
    //        array('50% is less than 100%', '50% is less than 100%', 'Text between % signs is allowed, if there are spaces.'),
    //    );
    //}

    ///**
    // * @test
    // */
    //public function paramProvider()
    //{
    //    return [
    //        [
    //            [
    //                'fuzzy' => 'bear',
    //                'foo' => 'bar'
    //            ]
    //        ],
    //        [
    //            ['fuzzy' => 'bear']
    //        ]
    //    ];
    //}


    ///**
    // * @test
    // */
    //public function testReplaceString()
    //{
    //    $this->params->set('%replace%', 'some string');
    //    $this->params->get('%replace%');

    //    $this->assertSame('some string got replaced here', $this->params->replaceString('%replace% got replaced here'));
    //}

    ///**
    // * @test
    // */
    //public function testSetKey()
    //{
    //    $this->params->set('%key%', 'value');

    //    $this->assertTrue($this->params->has('%key%'));

    //    $this->assertFalse($this->params->has('$key$'));
    //    $this->assertFalse($this->params->has('@key@'));
    //}

    ///**
    // * @test
    // */
    //public function testGetKey()
    //{
    //    $this->params->set('%key%', 'value');
    //    $this->assertSame('value', $this->params->get('%key%'));
    //}
}
