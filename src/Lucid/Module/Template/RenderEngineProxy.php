<?php

/*
 * This File is part of the Lucid\Module\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template;

/**
 * @class RenderEngineProxy
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RenderEngineProxy implements PhpEngineInterface
{
    private $engine;

    public function __construct(PhpEngineInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * {@inheritdoc}
     */
    public function extend($template)
    {
        return $this->engine->extend($template);
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
    public function insert($template, array $vars = [], array $parameters = [])
    {
        return $this->engine->insert($template, $vars, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function func()
    {
        return call_user_func_array($this->engine, 'func', func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function escape($string)
    {
        return $this->engine->escape($string);
    }
}
