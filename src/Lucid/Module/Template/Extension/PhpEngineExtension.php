<?php

/*
 * This File is part of the Lucid\Module\Template\Extension package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Extension;

/**
 * @class PhpEngineExtension
 *
 * @package Lucid\Module\Template\Extension
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PhpEngineExtension implements ExtensionInterface
{
    private $engine;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function functions()
    {
        return [
            new TemplateFunction('insert', [$this, 'insertTemplate']),
            new TemplateFunction('section', [$this, 'startSection']),
            new TemplateFunction('endsection', [$this, 'endSection']),
        ];
    }
}
