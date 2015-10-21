<?php

/*
 * This File is part of the Lucid\Common package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Contract;

/**
 * @interface Arrayable
 *
 * @package lucid/common
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface Jsonable
{
    public function toJson();
}
