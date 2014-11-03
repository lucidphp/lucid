<?php

/*
 * This File is part of the Lucid\Adapter\Twig package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Twig;

use Twig_Environment;
use Lucid\Module\Template\ViewAwareInterface;
use Lucid\Module\Template\Traits\ViewAwareTrait;

/**
 * @class TwigEnvironment
 *
 * @package Lucid\Adapter\Twig
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TwigEnvironment extends Twig_Environment implements ViewAwareInterface
{
    use ViewAwareTrait;
}
