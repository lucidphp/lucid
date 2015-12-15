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
 * @class RoutingExtension
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RoutingExtension extends AbstractExtension
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function functions()
    {
        return [
            new TemplateFunction('call_route', [$this, 'callRoute'])
        ];
    }

    /**
     * callRoute
     *
     * @param mixed $name
     * @param array $parameters
     * @param array $options
     *
     * @return string
     */
    public function callRoute($name, array $parameters = [], array $options = [])
    {
        try {
            $content = $this->router->dispatchRoute($name, $parameters, $options);
        } catch (\Exception $e) {
            return sprintf('<p style="display:block;boder:1px solid red;" class="error">%s</p>', $e->getMessage());
        }

        return is_string($content) ? $content : '';
    }
}
