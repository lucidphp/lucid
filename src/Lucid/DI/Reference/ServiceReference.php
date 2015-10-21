<?php

/*
 * This File is part of the Lucid\DI\Reference package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Reference;

/**
 * @class ServiceReference
 *
 * @package Lucid\DI\Reference
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ServiceReference implements ServiceReferenceInterface
{
    /**
     * id
     *
     * @var string
     */
    private $id;

    /**
     * Constructor.
     *
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getId();
    }
}
