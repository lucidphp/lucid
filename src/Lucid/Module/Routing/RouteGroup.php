<?php

/*
 * This File is part of the Lucid\Module\Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing;

/**
 * Helper class for generating RoutCollection objects.
 *
 * @class RouteGroup
 *
 * @package Lucid\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class RouteGroup
{
    /**
     * prefix
     *
     * @var string
     */
    protected $prefix;

    /**
     * parent
     *
     * @var parent
     */
    protected $parent;

    /**
     * requirements
     *
     * @var array
     */
    protected $requirements;

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
    protected function setPrefix($prefix)
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
    protected function setRequirements(array $requirements)
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
    protected function filterRequirements(array $requirements)
    {
        $keys = ['host', 'schemes'];

        return array_intersect_key($requirements, array_flip($keys));
    }
}
