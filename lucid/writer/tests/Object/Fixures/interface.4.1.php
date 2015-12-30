<?php

namespace Acme;

/**
 * @interface FooInterface
 */
interface FooInterface
{
    /** @var int */
    const T_FOO = 12;

    /**
     * setFoo
     *
     * @return void
     */
    public function setFoo();

    /**
     * setBar
     *
     * @return void
     */
    public function setBar();
}
