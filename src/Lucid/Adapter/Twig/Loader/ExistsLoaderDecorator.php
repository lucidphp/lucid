<?php

/*
 * This File is part of the Lucid\Adapter\Twig\Loader package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Twig\Loader;

use Twig_ExistsLoaderInterface;

use Lucid\Template\ViewAwareInterface;

/**
 * @class ExistsLoaderProxy
 *
 * @package Lucid\Adapter\Twig\Loader
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ExistsLoaderDecorator extends LoaderDecorator implements Twig_ExistsLoaderInterface
{
    public function __construct(ViewAwareInterface $view, Twig_ExistsLoaderInterface $loader)
    {
        parent::__construct($view, $loader);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return $this->loader->exists($name);
    }
}
