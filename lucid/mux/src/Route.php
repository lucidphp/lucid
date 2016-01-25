<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux;

use Closure;
use LogicException;

/**
 * @class
 * @see RouteInterface
 * @see Serializable
 */
class Route implements RouteInterface
{
    /** @var string */
    private $pattern;

    /** @var string|callable */
    private $handler;

    /** @var array */
    private $methods;

    /** @var string */
    private $host;

    /** @var array */
    private $defaults;

    /** @var array */
    private $constraints;

    /** @var array */
    private $schemes;

    /** @var RouteContextInterface */
    private $context;

    /** @var array */
    private static $keys = [
        'pattern',  'handler',     'methods', 'host',
        'defaults', 'constraints', 'schemes', 'context',
    ];

    /**
     * Constructor.
     *
     * @param string $pattern        the route pattern
     * @param mixed $handler         the route handler
     * @param string|array $methods  the supported http methods.
     * @param string $host           the required host
     * @param array $defaults        default parameters
     * @param array $constraints     parameter constraints
     * @param array $schemes         supported url schemes
     */
    public function __construct(
        $pattern,
        $handler,
        array $methods = null,
        $host = null,
        array $defaults = null,
        array $constraints = null,
        array $schemes = null
    ) {
        $this->pattern     = $pattern;
        $this->handler     = $handler;
        $this->host        = $host;
        $this->defaults    = $defaults ?: [];
        $this->constraints = $constraints ?: [];

        $this->setMethods($methods ?: ['GET']);
        $this->setSchemes($schemes ?: ['http', 'https']);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMethod($method)
    {
        return in_array(strtoupper($method), $this->methods);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getContext()
    {
        if (null === $this->context) {
            $this->context = call_user_func($this->getParserFunc(), $this);
        }

        return $this->context;
    }

    /**
     * Serializes the route
     *
     * @return string the serialized data of this route.
     */
    public function serialize()
    {
        if ($this->getHandler() instanceof Closure) {
            throw new LogicException('Cannot serialize handler.');
        }

        return serialize(array_combine(static::$keys, array_map(function ($key) {
            return call_user_func([$this, 'get'.ucfirst($key)]);
        }, static::$keys)));
    }

    /**
     * Unserializes the route
     *
     * @param string $data
     *
     * @return void.
     */
    public function unserialize($data)
    {
        $data = unserialize($data);

        foreach (static::$keys as $key) {
            $this->{$key} = $data[$key];
        }
    }

    /**
     * Get the callable that parses the route into tokens.
     *
     * @return callable
     */
    protected function getParserFunc()
    {
        return __NAMESPACE__.'\Parser\Standard::parse';
    }

    /**
     * Sets supported request methods.
     *
     * @param mixed $methods string or array of strings containing accepted
     * methods.
     *
     * @return void
     */
    private function setMethods(array $methods)
    {
        $this->methods = array_keys(array_change_key_case(array_flip($methods), CASE_UPPER));
    }

    /**
     * Sets the schemes.
     *
     * @param mixed $schemes string or array of strings containing accepted
     * schemes.
     *
     * @return void
     */
    private function setSchemes(array $schemes)
    {
        $this->schemes = array_keys(array_change_key_case(array_flip($schemes), CASE_LOWER));
    }
}
