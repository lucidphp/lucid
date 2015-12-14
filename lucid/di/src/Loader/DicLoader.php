<?php

/*
 * This File is part of the Lucid\DI\Loader package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Loader;

/**
 * @class DicLoader
 *
 * @package Lucid\DI\Loader
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class DicLoader implements LoaderInterface
{
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainerBuilder(ContainerBuilderInterface $container)
    {
        $this->container = $container;
    }

    public function getContainerBuilder()
    {
        return $this->container;
    }
}
