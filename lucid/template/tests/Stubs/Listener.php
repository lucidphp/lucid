<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Tests\Stubs;

use Lucid\Template\Listener\ListenerInterface;
use Lucid\Template\Data\TemplateDataInterface;

/**
 * @class Listener
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Listener implements ListenerInterface
{
    private $test;

    public function __construct(callable $callback = null)
    {
        $this->test = $callback;
    }

    public function onRender(TemplateDataInterface $data)
    {
        if (null !== $this->test) {
            call_user_func_array($this->test, func_get_args());
        }
    }
}
