<?php

/*
 * This File is part of the Lucid\Adapter\Twig\Extension package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Twig\Extension;

use Twig_Extension;
use Twig_SimpleFunction;
use Lucid\Routing\RouteDispatcherInterface;
use Lucid\Routing\Http\UrlGeneratorInterface;

/**
 * @class Routing
 *
 * @package Lucid\Adapter\Twig\Extension
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RoutingExtension extends Twig_Extension
{
    private $url;
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouteDispatcherInterface $dispatcher, UrlGeneratorInterface $url)
    {
        $this->dispatcher = $dispatcher;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucid_routing';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('call_route', [$this, 'displayRoute'])
        ];
    }

    /**
     * displayRoute
     *
     * @param string $name
     * @param array $parameters
     * @param array $options
     *
     * @return string
     */
    public function displayRoute($name, array $parameters = [], array $options = [])
    {
        try {
            return $this->doDisplayRoute($name, $parameters, $options);
        } catch (\Exception $e) {
            ob_end_clean();
        }

        return '';
    }

    private function doDisplayRoute($name, array $parameters, array $options)
    {
        ob_start();
        if (null === $res = $this->dispatcher->dispatchRoute($name, $parameters, $options)) {
            $res = ob_get_contents();
        }
        ob_end_clean();

        return $res;
    }
}
