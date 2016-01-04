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

use ReflectionObject;
use Lucid\Common\Helper\Str;

/**
 * @class AbstractProvider
 *
 * @package Lucid\Package
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractProvider implements ProviderInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $alias;

    /** @var string */
    private $path;

    /** @var string */
    private $namespace;

    /** @var ReflectionObject */
    private $reflection;

    /**
     * {@inheritdoc}
     */
    public function requires()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $class =  $this->getConfigClassName();

        return new $class($this);
    }

    /**
     * Returns the class name of the configuragtion file.
     *
     * @return string
     */
    public function getConfigClassName()
    {
        return sprintf('%s\Config', $this->getNamespace());
    }

    /**
     * {@inheritdoc}
     */
    final public function getNamespace()
    {
        if (null === $this->namespace) {
            $this->namespace = $this->getPackageReflection()->getNamespaceName();
        }

        return $this->namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        if (null === $this->name) {
            $this->name = $this->getPackageReflection()->getShortName();
        }

        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    final public function getPath()
    {
        if (null === $this->path) {
            $this->path = dirname($this->getPackageReflection()->getFileName());
        }

        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        if (null === $this->alias) {
            if (false !== ($pos = strripos($name = $this->getName(), $this->getPostFix())) && 0 !== $pos) {
                $base = substr($name, 0, $pos);
            } else {
                $base = $name;
            }

            $this->alias = Str::lowDash($base);
        }

        return $this->alias;
    }

    /**
     * Returns the ReflectionObject of this instance.
     *
     * @return \ReflectionObject
     */
    final public function getPackageReflection()
    {
        if (null === $this->reflection) {
            $this->reflection = new ReflectionObject($this);
        }

        return $this->reflection;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostFix()
    {
        return 'provider';
    }
}
