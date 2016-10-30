<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux;

use LogicException;
use Lucid\Mux\Meta\AttributesInterface;
use Lucid\Mux\Parser\Standard;

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

    /** @var AttributesInterface */
    private $attributes;

    /** @var array */
    private const KEYS = [
        'pattern',  'handler',     'methods', 'host',
        'defaults', 'constraints', 'schemes', 'context',
    ];

    /**
     * Constructor.
     *
     * @param string $pattern     the route pattern
     * @param mixed $handler      the route handler
     * @param array $methods      the supported http methods.
     * @param string $host        the required host
     * @param array $defaults     default parameters
     * @param array $constraints  parameter constraints
     * @param array $schemes      supported url schemes
     * @param array $attrs        route  attributes
     */
    public function __construct(
        string $pattern,
        $handler,
        array $methods = null,
        string $host = null,
        array $defaults = null,
        array $constraints = null,
        array $schemes = null,
        array $attrs = null
    ) {
        $this->pattern     = $pattern;
        $this->handler     = $handler;
        $this->host        = $host;
        $this->defaults    = $defaults ?: [];
        $this->constraints = $constraints ?: [];
        $this->attributes  = $attrs ?: $attrs;

        $this->setMethods($methods ?: explode('|', self::DEFAULT_METHODS));
        $this->setSchemes($schemes ?: explode('|', self::DEFAULT_SCHEMES));
    }

    /**
     * {@inheritdoc}
     */
    public function getMethods() : array
    {
        return $this->methods;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMethod(string $method) : bool
    {
        return in_array(strtoupper($method), $this->methods);
    }

    /**
     * {@inheritdoc}
     */
    public function getHandler() /*: callable | string */
    {
        return $this->handler;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemes() : array
    {
        return $this->schemes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasScheme(string $scheme) : bool
    {
        return in_array(strtolower($scheme), $this->schemes);
    }

    /**
     * {@inheritdoc}
    */
    public function getPattern() : string
    {
        return $this->pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost() : ?string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaults() : array
    {
        return $this->defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(string $var) /*: ?mixed*/
    {
        return isset($this->defaults[$var]) ? $this->defaults[$var] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints() : array
    {
        return $this->constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraint(string $param) : string
    {
        return isset($this->constraints[$param]) ? $this->constraints[$param] : null;
    }

    public function getAttributes() : AttributesInterface
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext() : RouteContextInterface
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
    public function serialize() : string
    {
        if (!is_string($this->getHandler())) {
            throw new LogicException('Cannot serialize handler.');
        }

        return serialize(array_combine(self::KEYS, array_map(function ($key) {
            return $this->{'get'.ucfirst($key)}();
        }, self::KEYS)));
    }

    /**
     * De-serializes the route.
     *
     * @param string $data
     *
     * @return void.
     */
    public function unserialize($data) : void
    {
        $data = unserialize($data);

        foreach (self::KEYS as $key) {
            $this->{$key} = $data[$key];
        }
    }

    /**
     * Get the callable that parses the route into tokens.
     *
     * @return callable
     */
    protected function getParserFunc() : callable
    {
        return Standard::class . '::parse';
    }

    /**
     * Sets supported request methods.
     *
     * @param mixed $methods string or array of strings containing accepted
     * methods.
     *
     * @return void
     */
    private function setMethods(array $methods) : void
    {
        $this->methods = array_keys(array_change_key_case(array_flip($methods), CASE_UPPER));
    }

    /**
     * Sets the schemes.
     *
     * @param array $schemes array of strings containing accepted
     * schemes.
     *
     * @return void
     */
    private function setSchemes(array $schemes) : void
    {
        $this->schemes = array_keys(array_change_key_case(array_flip($schemes), CASE_LOWER));
    }
}
