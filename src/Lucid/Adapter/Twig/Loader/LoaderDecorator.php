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

use Twig_LoaderInterface;

/**
 * @class LoaderProxy
 *
 * @package Lucid\Adapter\Twig\Loader
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class LoaderDecorator
{
    protected $view;
    protected $loader;

    public function __construct(ViewAwareInterface $view, Twig_ExistsLoaderInterface $loader)
    {
        $this->view = $view;
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return $this->loader->getSource($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $this->loader->getCacheKey($name);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        return $this->isFreach($name, $time);
    }
}
