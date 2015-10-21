<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Tests\Session;

use Lucid\Http\Session\Session;
use Lucid\Http\Session\Data\Attributes;
use Lucid\Http\Session\Storage\TransitoryArrayStorage as Store;

/**
 * @class SessionTest
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $session = new Session(new Store);
    }

    /** @test */
    public function itShouldNotBeClosedActiveOrStarted()
    {
        $session = new Session(new Store);

        $this->assertFalse($session->isClosed());
        $this->assertFalse($session->isActive());
        $this->assertFalse($session->isStarted());
    }

    /** @test */
    public function itShouldBeStartedAndActive()
    {
        $session = new Session(new Store);
        $session->start();

        $this->assertFalse($session->isClosed());
        $this->assertTrue($session->isActive());
        $this->assertTrue($session->isStarted());
    }

    /** @test */
    public function itShouldBeClosed()
    {
        $session = new Session(new Store);
        $session->start();
        $session->save();

        $this->assertTrue($session->isClosed());
        $this->assertFalse($session->isActive());
        $this->assertFalse($session->isStarted());
    }

    /** @test */
    public function itShouldGetItsName()
    {
        $session = new Session(new Store($name = 'NEWSESS'));

        $this->assertSame($name, $session->getName());

        $session->setName($name = 'NEWNAME');

        $this->assertSame($name, $session->getName());
    }

    /** @test */
    public function itShouldGetItsId()
    {
        $session = new Session(new Store);

        $this->assertNull($session->getId());

        $session->start();
        $this->assertInternalType('string', $session->getId());

        $session->setId($id = 'newid');

        $this->assertSame($id, $session->getId());
    }

    /** @test */
    public function itShouldRegenerateId()
    {
        $session = new Session(new Store);
        $session->start();
        $id = $session->getId();
        $session->regenerate();

        $this->assertTrue($id !== $session->getId());
    }

    /** @test */
    public function itShouldStoreSession()
    {
        $session = new Session(new Store);

        $this->assertTrue($session->save());
    }

    /** @test */
    public function itShouldSetAndAgetValuesOnDefaultAttributes()
    {
        $session = new Session($st = new Store, new Attributes('session', '_sess'));
        $data = [];
        $st->setSessionData($data);

        $session->set('foo', 'bar');
        $this->assertTrue($session->has('foo'));
        $this->assertSame('bar', $session->get('foo'));

        $session->save();

        $this->assertArrayHasKey('_sess', $data);
    }

    /** @test */
    public function itShouldRegisterAttributesOnStore()
    {
        $session = new Session($st = new Store);
        $data = [];
        $st->setSessionData($data);

        $session->addAttributes(new Attributes('mydata', '_mattrs'));
        $session->getAttributes('_mattrs')->set('foo', 'bar');
        $session->save();

        $this->assertArrayHasKey('_mattrs', $data);
        $this->assertInternalType('array', $data['_attrs']);
    }
}
