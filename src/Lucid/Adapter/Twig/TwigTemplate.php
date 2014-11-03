<?php

/*
 * This File is part of the Lucid\Adapter\Twig package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Twig;

use Lucid\Module\Template\ViewAwareInterface;
use Lucid\Module\Template\ViewManagerInterface;

/**
 * @class TwigTemplate
 *
 * @package Lucid\Adapter\Twig
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class TwigTemplate extends \Twig_Template
{
    /**
     * {@inheritdoc}
     */
    public function display(array $context, array $blocks = [])
    {
        parent::display($this->getParameters($context), $blocks);
    }

    /**
     * getParameters
     *
     * @param array $content
     *
     * @return void
     */
    protected function getParameters(array $parameters)
    {
        if (($env = $this->getEnvironment()) instanceof ViewAwareInterface && $view = $env->getManager()) {
            return $this->getDataFromListener($view, $parameters);
        }

        return $parameters;
    }

    /**
     * getDataFromListener
     *
     * @param array $parameters
     *
     * @return array
     */
    protected function getDataFromListener(ViewManagerInterface $view, array $parameters)
    {
        $view->notifyListeners($name = $this->getTemplateName());

        if ($data = $view->flushData($name)) {
            return $data->all($parameters);
        }

        return $parameters;
    }
}
