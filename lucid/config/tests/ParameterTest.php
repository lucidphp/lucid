<?php

/*
 * This File is part of the Lucid\Config package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Config\Tests;

use Lucid\Config\Parameters;

/**
 * @class AliasTest
 *
 * @package Selene\Module\DI\Tests\Reference
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ParameterTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Config\ParameterInterface', new Parameters);
    }

    /** @test */
    public function itShouldSetParameter()
    {
        $params = $this->newParameters();
        $params->set('foo', 'bar');

        $this->assertTrue($params->has('foo'));
    }

    /** @test */
    public function itShouldGetParameter()
    {
        $params = $this->newParameters(['foo' => 'bar']);

        $this->assertSame('bar', $params->get('foo'));
    }

    /** @test */
    public function itShouldThrowIfParameterDoesNotExist()
    {
        $params = $this->newParameters();

        try {
            $params->get('foo');
        } catch (\Lucid\Config\Exception\ParameterException $e) {
            $this->assertSame('Parameter "foo" is not defined.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldRemoveParameter()
    {
        $params = $this->newParameters(['foo' => 'bar']);
        $params->remove('foo');

        $this->assertFalse($params->has('foo'));
    }

    /** @test */
    public function itShouldReplaceParameters()
    {
        $params = $this->newParameters(['foo' => 'bar']);
        $params->replaceParams(['baz' => 'boom']);

        $this->assertFalse($params->has('foo'));
        $this->assertTrue($params->has('baz'));
    }

    /** @test */
    public function itShouldAccesParamsAsArray()
    {
        $params = $this->newParameters(['foo' => 'bar']);

        $this->assertTrue(isset($params['foo']));
        $this->assertSame('bar', $params['foo']);

        unset($params['foo']);
        $this->assertFalse(isset($params['foo']));

        $params['bar'] = 'bar';
        $this->assertTrue(isset($params['bar']));
    }

    /** @test */
    public function itShouldResolveVariablesInStrings()
    {
        $params = $this->newParameters(['foo' => 'bar', 'baz' => '%foo%']);
        $string = $params->resolveString('%foo% equals bar');
        $this->assertSame('bar equals bar', $string);

        $string = $params->resolveString('%baz% equals bar');
        $this->assertSame('bar equals bar', $string);

        Parameters::$leftDelim = '{';
        Parameters::$rightDelim = '}';
        Parameters::$leftEscDelim = '{';
        Parameters::$rightEscDelim = '}';

        $params = $this->newParameters(['foo' => 'bar', 'baz' => '{foo}']);
        $string = $params->resolveString('{foo} equals bar');
        $this->assertSame('bar equals bar', $string);

        $string = $params->resolveString('{baz} equals bar');
        $this->assertSame('bar equals bar', $string);
    }

    /** @test */
    public function itShouldResolveVariablesInArrays()
    {
        $obj = new \stdClass;
        $params = $this->newParameters(['foo' => 'bar', 'baz' => '%foo%']);
        $val = $params->resolveParam(['%baz%' => 'foo', 'obj' => $obj]);

        $this->assertSame(['bar' => 'foo', 'obj' => $obj], $val);
    }

    /** @test */
    public function itShouldResolveItsParameters()
    {
        $params = $this->newParameters($p = ['foo' => 'bar', 'baz' => '%foo%']);
        $this->assertSame($p, $params->all());
        $this->assertSame(['foo' => 'bar', 'baz' => 'bar'], $params->resolve()->all());

        $this->assertTrue($params->isResolved());
        $this->assertSame('bar', $params->get('baz'));
    }

    /** @test */
    public function itShouldDetectCircularReference()
    {
        $params = $this->newParameters(['foo' => '%baz%', 'baz' => '%foo%']);
        try {
            $params->resolve()->all();
        } catch (\Lucid\Config\Exception\ParameterException $e) {
            $this->assertSame('Parameter variable "baz" is referencing itself.', $e->getMessage());
        }

        Parameters::$leftDelim = '<';
        Parameters::$rightDelim = '>';

        $params = $this->newParameters(['foo' => '<baz>', 'baz' => '<foo>']);

        try {
        } catch (\Lucid\Config\Exception\ParameterException $e) {
            $this->assertSame('Parameter variable "baz" is referencing itself.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldEscapeVariables()
    {
        $params = $this->newParameters();

        $this->assertSame('foo', $params->escape('foo'));
        $this->assertSame('%%foo%%', $params->escape('%foo%'));
        $this->assertSame(['foo' => '%%bar%%'], $params->escape(['foo' => '%bar%']));
    }

    /** @test */
    public function itShouldUnescapeVariables()
    {
        $params = $this->newParameters();

        $this->assertSame('%foo%', $params->unescape('%%foo%%'));
        $this->assertSame(['foo' => '%bar%'], $params->unescape(['foo' => '%%bar%%']));
    }

    /** @test */
    public function itShouldIgnoreEscapedStrings()
    {
        $params = $this->newParameters(['bar' => 'baz']);

        $this->assertSame('foo %bar%', $params->resolveString('foo %%bar%%'));

        Parameters::$leftDelim = '{';
        Parameters::$rightDelim = '}';
        Parameters::$leftEscDelim = '{';
        Parameters::$rightEscDelim = '}';

        $params = $this->newParameters(['bar' => 'baz']);

        $this->assertSame('foo {bar}', $params->resolveString('foo {{bar}}'));
    }

    /** @test */
    public function itShouldBeMergable()
    {
        $paramsA = $this->newParameters(['foo' => 'bar']);
        $paramsB = $this->newParameters(['baz' => 'boom']);

        $paramsA->merge($paramsB);

        $this->assertTrue($paramsA->has('baz'));
        $this->assertTrue($paramsA->has('foo'));
    }

    /** @test */
    public function itShouldBeUnresolvedAfterMergingUnresolvedParams()
    {
        $paramsA = $this->newParameters(['foo' => 'bar']);
        $paramsB = $this->newParameters(['baz' => 'boom']);

        $paramsA->resolve();

        $paramsA->merge($paramsB);
        $this->assertFalse($paramsA->isResolved());
    }

    /** @test */
    public function itShouldResolvedAfterMergingResolvedParams()
    {
        $paramsA = $this->newParameters(['foo' => 'bar']);
        $paramsB = $this->newParameters(['baz' => 'boom']);

        $paramsA->resolve();
        $paramsB->resolve();

        $paramsA->merge($paramsB);
        $this->assertTrue($paramsA->isResolved());
    }

    /** @test */
    public function itShouldThrowIfMergingWithItself()
    {
        $params = $this->newParameters(['foo' => 'bar']);

        try {
            $params->merge($params);
        } catch (\LogicException $e) {
            $this->assertSame(
                sprintf('Cannot merge "%s" with its own instance.', get_class($params)),
                $e->getMessage()
            );
        }
    }

    protected function newParameters(array $args = [])
    {
        return new Parameters($args);
    }

    protected function setUp()
    {
        Parameters::$leftDelim = '%';
        Parameters::$rightDelim = '%';

        Parameters::$leftEscDelim = '%';
        Parameters::$rightEscDelim = '%';
    }
}
