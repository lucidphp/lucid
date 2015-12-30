<?php

/**
 * This File is part of the Lucid\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Tests\Object;

/**
 * @class AbstractWriterTest
 * @package Lucid\Writer
 * @version $Id$
 */
abstract class AbstractWriterTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    abstract public function itShouldBeInstantiable();

    /** @test */
    public function itShouldSetNamespaceAndFqn()
    {
        $cwr = $this->newObw('MyObject', 'Acme\Test');

        $this->assertSame('Acme\Test', $cwr->getNamespace(), 'Namespace should be "Acme\Test"');
        $this->assertSame('MyObject', $cwr->getName());
        $this->assertSame('\Acme\Test\MyObject', $cwr->getFqn());
    }


    /** @test */
    public function isShouldExtractNamespaceAndFqnFromName()
    {
        $cwr = $this->newObw('Acme\Test\MyObject');

        $this->assertSame('Acme\Test', $cwr->getNamespace());
        $this->assertSame('MyObject', $cwr->getName());
        $this->assertSame('\Acme\Test\MyObject', $cwr->getFqn());
    }

    /**
     * newObw
     *
     * @param string $name
     * @param string $namespace
     *
     * @return AbstractWriter
     */
    abstract protected function newObw($name = 'MyObject', $namespace = null);


    protected function getContents($file)
    {
        if (!is_file($path = __DIR__.'/Fixures/'.$file)) {
            throw new \InvalidArgumentException($file.' is not a valid file.');
        }

        return file_get_contents($path);
    }
}
