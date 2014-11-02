<?php

/*
 * This File is part of the Lucid\Module\Template\Helper package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Helper;

use Lucid\Module\Template\EngineInterface;

/**
 * @class AbstractHelper
 *
 * @package Lucid\Module\Template\Helper
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class AbstractHelper implements HelperInterface
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
     * getEngine
     *
     * @return EngineInterface
     */
    protected function getEngine()
    {
        return $this->engine;
    }
}
