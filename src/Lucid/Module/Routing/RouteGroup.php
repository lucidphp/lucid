<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing;

/**
 * @class Group
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
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
     * @param mixed $prefix
     * @param array $requirements
     * @param GroupDefinition $parent
     *
     * @access public
     */
    public function __construct($prefix, array $requirements, RouteGroup $parent = null)
    {
        $this->parent = $parent;

        $this->setPrefix($prefix);
        $this->setRequirements($requirements);
    }

    /**
     * hasParent
     *
     * @access public
     * @return boolean
     */
    public function hasParent()
    {
        return null !== $this->parent;
    }

    /**
     * getPrefix
     *
     * @access public
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * getRequirements
     *
     * @access public
     * @return array
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * setPrefix
     *
     *
     * @access protected
     * @return mixed
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
     * setRequirements
     *
     * @access protected
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
     * filterRequirements
     *
     * @param array $requirements
     *
     * @access protected
     * @return array
     */
    protected function filterRequirements(array $requirements)
    {
        $keys = ['host', 'schemes'];

        return array_intersect_key($requirements, array_flip($keys));
    }
}
