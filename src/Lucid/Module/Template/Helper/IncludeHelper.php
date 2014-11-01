<?php

/*
 * This File is part of the Lucid\Module\Template\Helper package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Helper;

/**
 * @class IncludeHelper
 *
 * @package Lucid\Module\Template\Helper
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class IncludeHelper implements HelperInterface
{
    public function getName()
    {
        return 'include';
    }

    public function setEngine($engine)
    {
        $this->engine = $engine;
    }

    public function execute(array $arguments)
    {

    }
}
