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

use Lucid\DI\ContainerBuilderInterface;

/**
 * @interface LoaderInterface
 *
 * @package Lucid\DI\Loader
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface LoaderInterface
{
    /**
     * setContainerBuilder
     *
     * @param ContainerBuilderInterface $container
     *
     * @return void
     */
    public function setContainerBuilder(ContainerBuilderInterface $container);

    /**
     * getContainerBuilder
     *
     * @return ContainerBuilderInterface
     */
    public function getContainerBuilder();
}
