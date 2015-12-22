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

/**
 * @class TemplateFunction
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TemplateFunction implements FunctionInterface
{
    /** @var string */
    private $name;

    /** @var callable */
    private $callable;

    /** @var array */
    private $options;

    /**
     * Constructor.
     *
     * @param string $name
     * @param callable $callable
     * @param array $options
     */
    public function __construct($name, callable $callable, array $options = [])
    {
        $this->name = (string)$name;
        $this->callable = $callable;
        $this->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function call(array $arguments = [])
    {
        return call_user_func_array($this->getCallable(), $arguments);
    }

    /**
     * getOption
     *
     * @param string $option
     *
     * @return mixed
     */
    public function getOption($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(...$args)
    {
        return $this->call($args);
    }

    /**
     * setOptions
     *
     * @param array $options
     *
     * @return void
     */
    private function setOptions(array $options)
    {
        $this->options = array_merge([
            'is_safe_html' => false
        ], $options);
    }
}
