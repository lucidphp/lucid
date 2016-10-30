<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux;

/**
 * Class RouteGroup
 * Helper class for generating RoutCollection objects.
 *
 * @package Lucid\Mux
 */
class RouteGroup
{
    /** @var string */
    private $prefix;

    /** @var RouteGroup */
    private $parent;

    /** @var array */
    private $requirements;

    /**
     * RouteGroup constructor.
     *
     * @param string $prefix the group prefix.
     * @param array $requirements the group requirements.
     * @param RouteGroup $parent the parent group.
     */
    public function __construct(string $prefix, array $requirements, RouteGroup $parent = null)
    {
        $this->parent = $parent;
        $this->setPrefix($prefix);
        $this->setRequirements($requirements);
    }

    /**
     * Tell if the group has a parent group.
     *
     * @return bool
     */
    public function hasParent() : bool
    {
        return null !== $this->parent;
    }

    /**
     * Get the group prefix.
     *
     * @return string the prefix.
     */
    public function getPrefix() : string
    {
        return $this->prefix;
    }

    /**
     * Get group requirements
     *
     * @return array the group requirements
     */
    public function getRequirements() : array
    {
        return $this->requirements;
    }

    /**
     * Set the group prefix.
     *
     * @param string $prefix group prefix.
     */
    private function setPrefix(string $prefix) : void
    {
        if (0 === strlen($prefix)) {
            throw new \InvalidArgumentException('Group prefix may not be empty.');
        }

        $prefix = '/'.trim($prefix, '/');

        $this->prefix = $this->hasParent() ? $this->parent->getPrefix() . $prefix : $prefix;
    }

    /**
     * Set the group requirements
     *
     * @param array $requirements
     */
    private function setRequirements(array $requirements) : void
    {
        $requirements = $this->filterRequirements($requirements);

        $this->requirements = $this->hasParent() ?
            array_merge($this->parent->getRequirements(), $requirements) :
            $requirements;
    }

    /**
     * Filter group requirements.
     *
     * @param array $requirements
     *
     * @return array the filtered requirements.
     */
    private function filterRequirements(array $requirements) : array
    {
        return array_intersect_key($requirements, array_flip(['host', 'schemes']));
    }
}
