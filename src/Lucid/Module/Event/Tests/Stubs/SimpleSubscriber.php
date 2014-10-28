<?php

/*
 * This File is part of the Lucid\Module\Event\Tests\Stubs package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Event\Tests\Stubs;

use Lucid\Module\Event\SubscriberInterface;

/**
 * @class SimpleSubscriber
 *
 * @package Lucid\Module\Event\Tests\Stubs
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SimpleSubscriber implements SubscriberInterface
{
    public $subject;
    public function __construct(\stdClass $subj)
    {
        $this->subject = $subj;
    }

    public function getSubscriptions()
    {
        return [
            'eventA' => 'onA',
            'eventB' => 'onB'
        ];
    }

    public function onA()
    {
        $this->subject->first = 'A';
    }

    public function onB()
    {
        $this->subject->second = 'B';
    }
}
