<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Extension;

use Lucid\Template\EngineInterface;
use Lucid\Template\PhpRenderInterface;

/**
 * @class PhpEngineExtension
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PhpEngineExtension extends AbstractExtension
{
    public function functions()
    {
        $engine = $this->getEngine();
        return [
            new TemplateFunction('section', [$engine, 'section']),
            new TemplateFunction('endsection', [$engine, 'endsection']),
            new TemplateFunction('insert', [$engine, 'insert']),
            new TemplateFunction('extend', [$engine, 'extend']),
            new TemplateFunction('escape', [$engine, 'escape']),
            new TemplateFunction('func', [$engine, 'func']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setEngine(EngineInterface $engine)
    {
        if (!$engine instanceof PhpRenderInterface) {
            throw new \InvalidArgumentException;
        }

        parent::setEngine($engine);
    }
}
