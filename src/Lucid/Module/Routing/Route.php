<?php

/*
 * This File is part of the Lucid\Module\Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing;

/**
 * @class Route
 *
 * @package Lucid\Module\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Route implements RouteInterface, \Serializable
{
    private $pattern;
    private $handler;
    private $methods;
    private $host;
    private $defaults;
    private $constraints;
    private $schemes;
    private $context;

    /**
     * Constructor
     *
     * @param mixed $pattern
     * @param mixed $handler
     * @param string $methods
     * @param mixed $host
     * @param array $defaults
     * @param array $constraints
     * @param array $schemes
     * @param array $defaults
     *
     * @return void
     */
    public function __construct(
        $pattern,
        $handler,
        $methods = 'GET',
        $host = null,
        array $defaults = [],
        array $constraints = [],
        $schemes = null
    ) {
        $this->pattern = $pattern;
        $this->handler = $handler;

        $this->setMethods($methods);
        $this->setSchemes($schemes);

        $this->host        = $host;
        $this->defaults    = $defaults;
        $this->constraints = $constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * hasMethod
     *
     * @param string $method
     *
     * @return void
     */
    public function hasMethod($method)
    {
        return in_array(strtoupper($method), $this->methods);
    }

    /**
     * getHandler
     *
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemes()
    {
        return $this->schemes;
    }

    /**
     * hasScheme
     *
     * @param mixed $scheme
     *
     * @return void
     */
    public function hasScheme($scheme)
    {
        return in_array(strtolower($scheme), $this->schemes);
    }

    /**
     * {@inheritdoc}
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault($var)
    {
        return isset($this->defaults[$var]) ? $this->defaults[$var] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraint($param)
    {
        return isset($this->constraints[$param]) ? $this->constraints[$param] : null;
    }

    /**
     * getExpression
     *
     *
     * @return void
     */
    public function getContext()
    {
        if (null === $this->context) {
            $this->expression = call_user_func($this->getParserFunc(), $this);
        }

        return $this->expression;
    }

    /**
     * serialize
     *
     * @return void
     */
    public function serialize()
    {
        if (!$this->isSerializableHandler($this->gethandler())) {
            throw new \RuntimeException('Cannot serialize handler.');
        }

        return  serialize([
            'pattern'     => $this->getPattern(),
            'handler'     => $this->getHandler(),
            'methods'     => $this->getMethods(),
            'host'        => $this->getHost(),
            'defaults'    => $this->getDefaults(),
            'constraints' => $this->getConstraints(),
            'schemes'     => $this->getSchemes(),
            'context'     => $this->getContext(),
        ]);
    }

    /**
     * unserialize
     *
     * @param mixed $data
     *
     * @return void
     */
    public function unserialize($data)
    {
        $data = unserialize($data);
        $this->pattern     = $data['pattern'];
        $this->handler     = $data['handler'];
        $this->methods     = $data['methods'];
        $this->host        = $data['host'];
        $this->constraints = $data['constraints'];
        $this->defaults    = $data['defaults'];
        $this->schemes     = $data['schemes'];
        $this->context     = $data['context'];
    }

    /**
     * getParserFunc
     *
     * @return callable
     */
    protected function getParserFunc()
    {
        return __NAMESPACE__.'\RouteParser::parse';
    }

    /**
     * setMethods
     *
     * @param mixed $methods
     *
     * @return void
     */
    protected function setMethods($methods)
    {
        if (is_array($methods)) {
            $methods = implode('|', array_values($methods));
        }

        $this->methods = explode('|', strtoupper($methods));
    }

    /**
     * setSchemes
     *
     * @param mixed $schemes
     *
     * @return void
     */
    protected function setSchemes($schemes)
    {
        if (is_array($schemes)) {
            $schemes = implode('|', array_values($schemes));
        }

        $this->schemes = 0 < strlen($schemes) ? explode('|', strtolower($schemes)) : ['http', 'https'];
    }

    /**
     * isSerializableHandler
     *
     * @param mixed $handler
     *
     * @return boolean
     */
    protected function isSerializableHandler($handler)
    {
        return !$handler instanceof \Closure;
    }
}
