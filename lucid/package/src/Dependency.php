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

use Lucid\Package\Exception\RequirementException;

/**
 * @class Dependency
 *
 * @package Lucid\Package
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Dependency
{
    /** @var array */
    private $current = [];

    /**
     * Get the packages in required order.
     *
     * @return array
     */
    public function getSorted(RepositoryInterface $providers)
    {
        $req = [];

        foreach ($providers->all() as $key => $provider) {
            foreach ($this->getRequirements($provider, $providers, true) as $pkg) {
                if (array_key_exists($alias = $pkg->getAlias(), $req)) {
                    continue;
                }

                $req[$alias] = $pkg;
            }
        }

        return $req;
    }

    /**
     * Get dependent packages of a package.
     *
     * If $includeSelf is true, the package will be included as last member.
     *
     * @param PackageInterface $provider     the package
     * @param boolean          $includeSelf include the current package observed
     *
     * @throws \InvalidArgumentException if a required package does not exists.
     * @throws \InvalidArgumentException if there's a circular reference.
     *
     * @return array of packages
     */
    public function getRequirements(ProviderInterface $provider, RepositoryInterface $providers, $includeSelf = false)
    {
        $this->current[$provider->getAlias()] = true;

        $req =  $this->doGetRequirements($provider, $providers);

        unset($this->current[$provider->getAlias()]);

        if ($includeSelf) {
            $req[$provider->getAlias()] = $provider;
        }

        return $req;
    }

    /**
     * Get the package requirements
     *
     * @param PackageInterface $provider
     * @param array $res
     *
     * @return array
     */
    protected function doGetRequirements(ProviderInterface $provider, RepositoryInterface $providers, array &$res = [])
    {
        $alias = $provider->getAlias();
        $requirements = (array)$provider->requires();

        foreach ($requirements as $req) {

            $optional = $this->isOptional($req);
            $req = rtrim($req, '?');

            if (!$providers->has($req)) {

                if ($optional) {
                    continue;
                }

                throw RequirementException::missingPackage($alias, $req);
            }

            if (isset($this->current[$req]) || $req === $alias) {
                throw RequirementException::circularReference($alias, $req);
            }

            $this->doGetRequirements($providers->get($req), $providers, $res);

            $res[$req] = $providers->get($req);
        }

        return $res;
    }

    /**
     * isOptional
     *
     * @param string $provider
     *
     * @return bool
     */
    private function isOptional($provider)
    {
        return '?' === $provider[strlen($provider) - 1];
    }
}
