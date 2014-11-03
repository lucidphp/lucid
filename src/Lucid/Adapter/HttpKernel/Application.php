<?php

/*
 * This File is part of the Lucid\Adapter\HttpKernel package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @class Application
 *
 * @package Lucid\Adapter\HttpKernel
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Application implements ApplicationInterface
{
    const VERSION = '0.0.1-dev';

    /**
     * options
     *
     * @var array
     */
    protected $options;

    /**
     * parameters
     *
     * @var mixed
     */
    protected $parameters;

    /**
     * container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * Possible options are
     *
     * - env:
     *   The environment name of the application currently running in.
     * - debug:
     *   Boolean if debugging should be enabled.
     * - container_cache_path:
     *   Pathname of the container cache.
     *
     * @param array $options
     * @param array $parameters
     */
    public function __construct(array $options = [], array $parameters = [])
    {
        $this->setOptions($options);
        $this->setParameters($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function run(Request $request = null, $catch = null)
    {
        return $this->terminate(
            $request ?: $this->newRequest(),
            $this->handle(self::MASTER_REQUEST, $catch ?: $this->debug)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->getHttpStack()->handle($request, $type, $catch);
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(Request $request, Response $response)
    {
    }

    /**
     * Get the DIC.
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Get the main http kernel.
     *
     * @return HttpKernelInterface
     */
    protected function getHttpKernel()
    {
        $this->getContainer()->get($this->parameters['http_kernel_service_name']);
    }

    /**
     * Get the main http stack.
     *
     * @return HttpStack
     */
    protected function getHttpStack()
    {
        $this->getContainer()->get($this->parameters['http_stack_service_name']);
    }

    /**
     * Create a new request object.
     *
     * @return Request
     */
    protected function newRequest()
    {
        return Request::createFromGlobals();
    }

    protected function setOptions(array $options)
    {
        $options = array_merge($options, $this->getDefaultOptions());
    }

    /**
     * setOptions
     *
     * @param array $options
     *
     * @return void
     */
    protected function setParameters(array $options)
    {
        $this->parameters = array_merge($options, $this->getdefaultParameters());
    }

    /**
     * getDefaultOptions
     *
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'env' => 'development',
            'debug' => true,
            'container_cache_path' => getcwd().'/cache/container'
        ];
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultParameters()
    {
        return [
            'http_kernel_service_name' => 'http.kernel',
            'http_stack_service_name' => 'http.stack',
            'http_stackbuilder_service_name' => 'http.stackbuilder'
        ];
    }
}
