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
 * @class TemplateFunction
 *
 * @package Lucid\Module\Template\Extension
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TemplateFunction implements FunctionInterface
{
    private $name;
    private $callable;
    private $options;

    /**
     * Constructor.
     *
     * @param string $name
     * @param callable $callable
     * @param array $options
     *
     * @return void
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
    public function __invoke()
    {
        return $this->call(func_get_args());
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
