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
use Lucid\Module\Template\Data\TemplateDataInterface;
use Lucid\Module\Template\Data\Data;

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
     * Render engine
     *
     * @var EngineInterface
     */
    private $engine;

    /**
     * Render listeners
     *
     * @var ListenerInterface[]
     */
    private $listeners;

    /**
     * Global parameters
     *
     * @var array
     */
    private $parameters;

    /**
     * Temporary data.
     *
     * @var array
     */
    private $data;

    /**
     * Constructor.
     *
     * @param EngineInterface $engine
     * @param array $listeners
     */
    public function __construct(EngineInterface $engine, array $listeners = [])
    {
        $this->engine = $engine;
        $this->data = [];
        $this->parameters = [];

        $this->setListeners($listeners);
    }

    /**
     * Set render listeners.
     *
     * @param array $listeners
     *
     * @return void
     */
    public function setListeners(array $listeners)
    {
        $this->listeners = [];

        foreach ($listeners as $name => $listener) {
            $this->addListener($name, $listener);
        }
    }

    /**
     * Add a render listener.
     *
     * @param string $name
     * @param ListenerInterface $listener
     *
     * @return void
     */
    public function addListener($name, ListenerInterface $listener)
    {
        $this->listeners[(string)$name] = $listener;
    }

    /**
     * {@inheritdoc}
     */
    public function flushData($name)
    {
        if (!isset($this->data[$name])) {
            return false;
        }

        $data = $this->data[$name];

        unset($this->data[$name]);

        return $data;
    }

    /**
     * Set global parameters.
     *
     * @param array $parameters
     *
     * @return void
     */
    public function setGlobals(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Add global parameters.
     *
     * @param array $parameters
     *
     * @return void
     */
    public function addGlobals(array $parameters)
    {
        $this->parameters = array_merge((array)$this->parameters, $parameters);
    }

    /**
     * Add a global parameter.
     *
     * @param string $key
     * @param mixed $parameter
     *
     * @return void
     */
    public function addGlobal($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function notifyListeners($name)
    {
        if (!isset($this->listeners[$name])) {
            return;
        }

        $this->listeners[$name]->onRender($this->data[$name] = new Data($this));
    }

    /**
     * {@inheritdoc}
     */
    public function render($template, array $parameters = [])
    {
        return $this->getEngineForTemplate($template)->render($template, $this->getParameters($parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function display($template, array $parameters = [])
    {
        if (($engine = $this->getEngineForTemplate($template)) instanceof DisplayInterface) {
            $engine->display($template, $this->getParameters($parameters));
        } else {
            echo $engine->render($template, $this->getParameters($parameters));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($template)
    {
        return $this->engine->supports($template);
    }

    /**
     * {@inheritdoc}
     *
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
     * getParameters
     *
     * @param array $parameters
     *
     * @return void
     */
    protected function getParameters(array $parameters)
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
