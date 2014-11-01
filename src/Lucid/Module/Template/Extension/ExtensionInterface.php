<?php

/*
 * This File is part of the Lucid\Module\Template\Extension package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Extension;

use Lucid\Module\Template\EngineInterface;

/**
 * @class ExtensionInterface
 *
 * @package Lucid\Module\Template\Extension
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ExtensionInterface
{
    /**
     * Register
     *
     * @param array $functions
     *
     * @return array
     */
    public function functions();
}
