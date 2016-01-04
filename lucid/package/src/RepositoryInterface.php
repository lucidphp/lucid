<?php

/*
 * This File is part of the Lucid\Package package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package;

/**
 * @interface RepositoryInterface
 *
 * @package Lucid\Package
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RepositoryInterface
{
    /**
     * Adds a Provider to the repository.
     *
     * @param ProviderInterface $provider
     *
     * @return void
     */
    public function add(ProviderInterface $provider);

    /**
     * Adds an array of providers to the repository.
     *
     * @param array $providers `ProviderInterface[]`;
     *
     * @return void
     */
    public function addProviders(array $providers);

    /**
     * Returns a provider.
     *
     * @param string $provider the name or alias of the provider.
     *
     * @return ProviderInterface
     */
    public function get($provider);

    /**
     * Returns true if it has given provider.
     *
     * @param string $provider
     *
     * @return bool
     */
    public function has($provider);

    /**
     * Returns all providers as array
     *
     * @return array `ProviderInterface[]`;
     */
    public function all();

    /**
     * hasDependencies
     *
     * @param string $provider
     *
     * @return bool
     */
    public function hasDependencies($provider);
}
