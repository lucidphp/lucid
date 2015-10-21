<?php

/*
 * This File is part of the Lucid\Template\Extension package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Extension;

use Lucid\Template\EngineInterface;

/**
 * @class AbstractExtension
 *
 * @package Lucid\Template\Extension
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractExtension implements ExtensionInterface
{
    private $engine;

    /**
     * {@inheritdoc}
     */
    public function setEngine(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * {@inheritdoc}
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * {@inheritdoc}
     */
    public function functions()
    {
        return [];
    }
}
