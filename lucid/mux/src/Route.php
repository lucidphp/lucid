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
use RuntimeException;

/**
 * @class
 * @see RouteInterface
 * @see Serializable
 */
class Route implements RouteInterface
{
    /** @var array */
    private static $keys = [
        'pattern', 'handler', 'methods', 'host',
        'defaults', 'constraints', 'schemes', 'context',
    ];

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
        array $methods = ['GET'],
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
        if (!$this->isSerializableHandler($this->getHandler())) {
            throw new RuntimeException('Cannot serialize handler.');
        }

        return array_map(static::$keys, function ($key) {
            $method = 'get'.ucfirst($key);

            return call_user_func([$this, $method]);
        });
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
            $this->${$key} = $data[$key];
        }
    }

    /**
     * Get the callable that parses the route into tokens.
     *
     * @return callable
     */
    protected function getParserFunc()
    {
        return __NAMESPACE__.'\Parser\DefaultParser::parse';
    }

    /**
     * Sets supported request methods.
     *
     * @param mixed $methods string or array of strings containing accepted
     * methods.
     *
     * @return void
     */
    private function setMethods($methods)
    {
        if (is_array($methods)) {
            $this->methods = array_keys(array_change_key_case(array_flip($methods), CASE_UPPER));
        } else {
            $this->methods = explode('|', strtoupper($methods));
        }
    }

    /**
     * Sets the schemes.
     *
     * @param mixed $schemes string or array of strings containing accepted
     * schemes.
     *
     * @return void
     */
    private function setSchemes($schemes)
    {
        if (is_array($schemes)) {
            $this->schemes = array_keys(array_change_key_case(array_flip($schemes), CASE_LOWER));
        } else {
            $this->schemes = 0 < strlen($schemes) ? explode('|', strtolower($schemes)) : ['http', 'https'];
        }
    }

    /**
     * isSerializableHandler
     *
     * @param mixed $handler
     *
     * @return bool
     */
    private function isSerializableHandler($handler)
    {
        return !$handler instanceof Closure;
    }
}
