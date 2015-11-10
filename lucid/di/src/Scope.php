<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI;

/**
 * @class ScopeInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class Scope
{
    const SINGLETON = 'singleton';

    const PROTOTYPE = 'prototype';

    private function __construct()
    {
    }
}
