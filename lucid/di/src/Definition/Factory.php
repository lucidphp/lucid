<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Definition;

use Lucid\DI\Scope;

/**
 * @interface FactoryInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Factory extends Service implements FactoryInterface
{
    /** @var bool */
    private $isStatic;

    /** @var string */
    private $factoryMethod;

    /**
     * Constructor
     *
     * @param string $class
     * @param string $method
     * @param bool $static
     * @param string $scope
     */
    public function __construct($class, $method, $static = true, Scope $scope = null)
    {
        $this->factoryMethod = $method;
        $this->isStatic      = (bool)$static;

        parent::__construct($class, [], $scope);
    }

    /**
     * {@inheritdoc}
     */
    public function getFactoryMethod()
    {
        return $this->factoryMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function isStatic()
    {
        return $this->isStatic;
    }
}
