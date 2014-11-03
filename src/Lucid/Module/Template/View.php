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

use Lucid\Module\Template\Listener\ListenerInterface;

/**
 * @class View
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class View implements ViewManagerInterface
{
    /**
     * engine
     *
     * @var EngineInterface
     */
    private $engine;

    /**
     * listeners
     *
     * @var ListenerInterface[]
     */
    private $listeners;

    /**
     * parameters
     *
     * @var array
     */
    private $parameters;

    /**
     * currentEngine
     *
     * @var array
     */
    private $currentEngine;

    /**
     * Constructor.
     *
     * @param EngineInterface $engine
     */
    public function __construct(EngineInterface $engine, array $listeners = [])
    {
        $this->engine = $engine;
        $this->parameters = [];

        $this->setListeners($listeners);
    }

    /**
     * setListeners
     *
     * @param array $listeners
     *
     * @return void
     */
    public function setListeners(array $listeners)
    {
        $this->listeners = [];

        foreach ($listeners as $name => $listeners) {
            $this->addListener($name, $listener);
        }
    }

    /**
     * addParameters
     *
     * @param array $parameters
     *
     * @return void
     */
    public function addParameters(array $parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * addListener
     *
     * @param mixed $name
     * @param ListenerInterface $listener
     *
     * @return void
     */
    public function addListener($name, ListenerInterface $listener)
    {
        $this->listeners[(string)$name] = $listener;
    }

    /**
     * notifyListeners
     *
     * @param mixed $template
     *
     * @return void
     */
    public function notifyListeners($template)
    {
        if (!isset($this->listeners[$name = (string)$template])) {
            return;
        }

        $this->listeners[$name]->onRender($this->getEngineForTemplate($template));
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException if no engine is found for the
     * given template.
     */
    public function getEngineForTemplate($template)
    {
        if ($this->engine instanceof DelegatingEngine) {
            return $this->prepareEngine($this->engine->resolveEngine($template));
        }

        if ($this->engine->supports($template)) {
            return $this->prepareEngine($this->engine);
        }

        throw new \InvalidArgumentException(sprintf('No engine found for template "%s".', (string)$template));
    }

    /**
     * {@inheritdoc}
     */
    public function render($template, array $parameters = [])
    {
        $parameters = $this->getParameters($parameters);

        $engine = $this->getEngineForTemplate($template);

        return $engine->render($template, $this->getParameters($parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function display($template, array $parameters = [])
    {
        $parameters = $this->getParameters($parameters);

        $this->notifyListeners($template);

        if (($engine = $this->getEngine()) instanceof DisplayInterface) {
            $engine->display($template, $this->getParameters($parameters));
        } else {
            echo $engine->render($template, $this->getParameters($parameters));
        }
    }

    /**
     * getEngine
     *
     * @return EngineInterface
     */
    protected function getEngine($template)
    {
        return $this->currentEngine ?: $this->getEngineForTemplate($template);
    }

    /**
     * getParameters
     *
     * @param array $parameters
     *
     * @return void
     */
    public function getParameters(array $parameters)
    {
        return empty($parameters) ? $this->parameters : array_merge($this->parameters, $parameters);
    }

    /**
     * prepareEngine
     *
     * @param EngineInterface $engine
     *
     * @return EngineInterface
     */
    protected function prepareEngine(EngineInterface $engine)
    {
        if ($engine instanceof ViewAwareInterface && $this !== $engine->getManager()) {
            $engine->setManager($this);
        }

        return $engine;
    }

}
