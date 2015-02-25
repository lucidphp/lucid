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
 * @class DelegatingEngine
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class DelegatingEngine implements EngineInterface, DisplayInterface
{
    /**
     * engines
     *
     * @var EngineInterface[]
     */
    private $engines;

    /**
     * Constructor.
     *
     * @param array $engines
     */
    public function __construct(array $engines = [])
    {
        $this->setEngines($engines);
    }

    /**
     * Sets the render engines.
     *
     * @param array $engines
     *
     * @return void
     */
    public function setEngines(array $engines)
    {
        $this->engines = [];

        foreach ($engines as $engine) {
            $this->addEngine($engine);
        }
    }

    /**
     * Adds a template Engine.
     *
     * @param EngineInterface $engine
     *
     * @return void
     */
    public function addEngine(EngineInterface $engine)
    {
        $this->engines[] = $engine;
    }

    /**
     * {@inheritdoc}
     */
    public function render($template, array $parameters = [])
    {
        return $this->getEngine($template)->render($template, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function display($template, array $parameters = [])
    {
        if (($engine = $this->getEngine($template)) instanceof DisplayInterface) {
            $engine->display($template, $parameters);
        } else {
            echo $engine->render($template, $parameters);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($template)
    {
        if ($engine = $this->resolveEngine($template)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($template)
    {
        if ($engine = $this->resolveEngine($template)) {
            return $engine->exists($template);
        }

        return false;
    }

    /**
     * resolveEngine
     *
     * @param mixed $template
     *
     * @return EngineInterface|bool false if no engine is false;
     */
    public function resolveEngine($template)
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($template)) {
                return $engine;
            }
        }

        return false;
    }

    private function getEngine($template)
    {
        if (!$engine = $this->resolveEngine($template)) {
            throw new \InvalidArgumentException(sprintf('No suitable engine found for template %s.', $template));
        }

        return $engine;
    }
}
