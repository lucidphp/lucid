<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux;

/**
 * Helper class for generating RoutCollection objects.
 *
 * @class RouteGroup
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class RouteGroup
{
    /** @var string */
    private $prefix;

    /** @var parent */
    private $parent;

    /** @var array */
    private $requirements;

    /**
     * Constructor.
     *
     * @param string $prefix the group prefix.
     * @param array $requirements the group requirements.
     * @param RouteGroup $parent the parent group.
     */
    public function __construct($prefix, array $requirements, RouteGroup $parent = null)
    {
        $this->parent = $parent;

        $this->setPrefix($prefix);
        $this->setRequirements($requirements);
    }

    /**
     * Tell if the group has a parent group.
     *
     * @return boolean
     */
    public function hasParent()
    {
        return null !== $this->parent;
    }

    /**
     * Get the group prefix.
     *
     * @return string the prefix.
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Get group requirements
     *
     * @return array the group requirements
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * Set the group prefix
     *
     * @return void
     */
    private function setPrefix($prefix)
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
     * @return void
     */
    private function setRequirements(array $requirements)
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
    private function filterRequirements(array $requirements)
    {
        $keys = ['host', 'schemes'];

        return array_intersect_key($requirements, array_flip($keys));
    }
}
