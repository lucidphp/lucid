<?php

/*
 * This File is part of the Lucid\Module\Common package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Common\Contract;

/**
 * @interface Cachable
 *
 * @package Lucid\Module\Common
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface Cachable
{
    /**
     * store
     *
     * @return void
     */
    public function store();

    /**
     * restore
     *
     * @return mixed
     */
    public function restore();
}
