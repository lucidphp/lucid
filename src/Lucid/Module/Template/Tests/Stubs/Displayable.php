<?php

/*
 * This File is part of the Lucid\Module\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Tests\Stubs;

use Lucid\Module\Template\RenderInterface;
use Lucid\Module\Template\DisplayInterface;
use Lucid\Module\Template\EngineInterface;

/**
 * @interface Renderer
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface Displayable extends EngineInterface, DisplayInterface
{
}
