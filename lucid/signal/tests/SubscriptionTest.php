<?php

namespace Lucid\Signal\Tests;

use Lucid\Signal\Subscription;

class SubscriptionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Signal\SubscriptionInterface', new Subscription([]));
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $subs = [
            'eventA' => 'onA',
            'eventB' => 'onB'
        ];

        $subscription = new Subscription($subs);

        $ret = [];
        foreach ($subscription->get() as $event => $s) {
            $ret[$event] = $s;
        }

        $this->assertEquals($subs, $ret);
    }
}
