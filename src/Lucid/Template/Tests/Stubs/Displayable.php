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

use Lucid\Template\RenderInterface;
use Lucid\Template\DisplayInterface;
use Lucid\Template\EngineInterface;

/**
 * @interface Renderer
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface Displayable extends EngineInterface, DisplayInterface
{
}
