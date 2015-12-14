<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template;

/**
 * @class RenderEngineProxy
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RenderEngineDecorator implements PhpRenderInterface
{
    private $engine;

    public function __construct(PhpRenderInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * {@inheritdoc}
     */
    public function extend($template, array $vars = [])
    {
        return $this->engine->extend($template, $vars);
    }

    /**
     * {@inheritdoc}
     */
    public function section($template)
    {
        return $this->engine->section($template);
    }

    /**
     * {@inheritdoc}
     */
    public function endsection()
    {
        return $this->engine->endsection();
    }

    /**
     * {@inheritdoc}
     */
    public function insert($template, array $replacements = [])
    {
        return $this->engine->insert($template, $replacements);
    }

    /**
     * {@inheritdoc}
     */
    public function func(...$args)
    {
        return call_user_func_array([$this->engine, 'func'], $args);
    }

    /**
     * {@inheritdoc}
     */
    public function escape($string)
    {
        return $this->engine->escape($string);
    }
}
