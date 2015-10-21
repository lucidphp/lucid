<?php

/*
 * This File is part of the Lucid\DI\Reflection package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Reflection;

/**
 * @class ContainerReflectionMethod
 *
 * @package Lucid\DI\Reflection
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContainerReflectionMethod
{
    const T_PUBLIC = 0;
    const T_PROTECTED = 1;
    const T_PRIVATE = 2;

    private $id;
    private $type;
    private $container;
    private $name;
    private $content;

    /**
     * Constructor.
     *
     * @param mixed $id
     * @param ContainerBuilderInterface $container
     * @param mixed $type
     *
     * @return void
     */
    public function __construct($id, ContainerBuilderInterface $container, $type)
    {
        $this->id = $id;
        $this->container = $container;
        $this->type = $type;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        if (null === $this->name) {
            $this->name = Container::factoryName($this->id);
        }

        return $this->name;
    }

    /**
     * getContent
     *
     * @return void
     */
    public function getContent()
    {
    }
}
