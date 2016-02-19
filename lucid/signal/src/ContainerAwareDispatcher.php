<?php

/*
 * This File is part of the Lucid\Signal package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Signal;

use Interop\Container\ContainerInterface;

/**
 * @class ContainerAwareDispatcher
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContainerAwareDispatcher extends EventDispatcher
{
    /** @var string */
    const M_SEPARATOR = '@';

    /** @var ContainerInterface */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function getHandler($handler)
    {
        if (is_string($handler)) {
            if ($this->hasAttachedMethod($handler)) {
                list($id, ) = explode(self::M_SEPARATOR, $handler);
            } else {
                $id = $handler;
            }

            if ($this->container->has($id)) {
                return $handler;
            }
        }

        return parent::getHandler($handler);
    }

    /**
     * hasAttachedMethod
     *
     * @param string $string
     *
     * @return bool
     */
    private function hasAttachedMethod($string)
    {
        return 1 === substr_count($string, self::M_SEPARATOR) && 0 !== strpos($string, self::M_SEPARATOR);
    }
}
