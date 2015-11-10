<?php

/*
 * This File is part of the Lucid\Config package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Config;

use Lucid\Config\Exception\ParameterException;

/**
 * @class Parameters
 * @see ParameterInterface
 *
 * @package Lucid\Config
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Parameters implements ParameterInterface, ResolvableInterface
{
    /**
     * parameters
     *
     * @var array
     */
    private $parameters;

    /**
     * resolved
     *
     * @var boolean
     */
    private $resolved;

    /**
     * resolving
     *
     * @var array
     */
    private $resolving;

    /**
     * resolvedParams
     *
     * @var array
     */
    private $resolvedParams;

    /**
     * leftDelim
     *
     * @var string
     */
    public static $leftDelim = '%';

    /**
     * rightDelim
     *
     * @var string
     */
    public static $rightDelim = '%';

    /**
     * leftEscDelim
     *
     * @var string
     */
    public static $leftEscDelim = '%';

    /**
     * rightEscDelim
     *
     * @var string
     */
    public static $rightEscDelim = '%';

    /**
     * Initialize parameter collection with data.
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->resolving = [];
        $this->replaceParams($params);
    }

    /**
     * Replaces all parameters.
     *
     * @param array $params
     *
     * @api
     *
     * @return void
     */
    public function replaceParams(array $params)
    {
        $this->setUnresolved();
        $this->parameters = array_change_key_case($params, CASE_LOWER);
    }

    /**
     * Sets a parameter.
     *
     * @param string $param
     * @param mixed $value
     *
     * @api
     *
     * @return void
     */
    public function set($param, $value)
    {
        $this->setUnresolved();
        $this->parameters[strtolower($param)] = $value;
    }

    /**
     * get
     *
     * @param mixed $param
     *
     * @api
     *
     * @throws \Selene\Module\DI\Exception\ParameterException
     * @return mixed
     */
    public function get($param)
    {
        if ($this->has($param)) {
            $params = $this->getParameters();

            return $params[strtolower($param)];
        }

        throw ParameterException::undefinedParameter($param);
    }

    /**
     * getRaw
     * @internal
     *
     * @return array
     */
    public function getRaw()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     *
     * Merging two objects will cause the current one to be reset to unresolved;
     *
     * @api
     *
     * @throws \LogicException And exception is thrown if you try to merge this instance with itself.
     * @return void
     */
    public function merge(ParameterInterface $parameters)
    {
        if ($this === $parameters) {
            throw new \LogicException(
                sprintf('Cannot merge "%s" with its own instance.', get_class($this))
            );
        }

        $this->parameters = array_merge($this->parameters, $parameters->getRaw());

        if (($parameters instanceof ResolvableInterface && $parameters->isResolved()) && $this->isResolved()) {
            $this->resolvedParams = array_merge($this->resolvedParams, $parameters->all());
        } else {
            $this->setUnresolved();
        }
    }

    /**
     * Check if a parameter exists.
     *
     * @param mixed $param
     *
     * @api
     *
     * @return boolean
     */
    public function has($param)
    {
        return array_key_exists(strtolower($param), $this->getParameters());
    }

    /**
     * Removes a parameter.
     *
     * @param mixed $param
     *
     * @api
     *
     * @return void
     */
    public function remove($param)
    {
        unset($this->parameters[$key = strtolower($param)]);
        unset($this->resolvedParams[$key]);
    }

    /**
     * Returns parameters depenging on its resolved state.
     *
     * @api
     *
     * @return array
     */
    public function all()
    {
        if ($this->resolved) {
            return $this->resolvedParams;

        }
        return $this->parameters;
    }

    /**
     * Resolves all parameters.
     *
     * @param mixed $parameters
     *
     * @api
     *
     * @return void
     */
    public function resolve()
    {
        if (!$this->isResolved()) {
            $this->doResolve();
        }

        return $this;
    }

    /**
     * Checks if the collection is resolved.
     *
     * @return boolean
     */
    public function isResolved()
    {
        return $this->resolved;
    }

    /**
     * Resolves a parameter or value with the given parameters `$this->parameters`.
     *
     * @param mixed $param
     *
     * @api
     *
     * @return mixed the resolved parameter or key (string, array, etc)
     */
    public function resolveParam($param)
    {
        if (is_string($param)) {
            $str = $this->resolveString($param);
            $this->resolving = [];

            return $str;
        }

        if (!is_array($param)) {
            return $param;
        }

        $resolved = [];

        foreach ($param as $key => $value) {
            if (is_string($key)) {
                $key = $this->resolveString($key);
            }

            $resolved[$key] = $this->resolveParam($value);
        }

        return $resolved;
    }

    /**
     * Resolves a string with the given parameters `$this->parameters`.
     *
     * @param string $string
     *
     * @api
     *
     * @return mixed the resolved parameter or key (string, array, etc)
     */
    public function resolveString($string)
    {
        // lets see if the string is single param
        if (preg_match($exp = $this->getMatchExp(), $string, $matches)) {
            if (isset($this->resolving[$matches[1]])) {
                throw ParameterException::circularReference($matches[1]);
            }

            $this->resolving[$matches[1]] = true;

            if ($this->has($str = $matches[1])) {
                if (is_string($value = $this->get($str))) {
                    $res =  $this->resolveString($value);
                } else {
                    throw ParameterException::nonScalarValues($string);
                }
            } else {
                $res = $string;
            }

            unset($this->resolving[$matches[1]]);

            return $res;
        }

        // otherwise replace the whole string:
        return preg_replace_callback($this->getReplaceExp(), function ($matches) {
            if (0 !== strlen($matches[1]) || 0 !== strlen($matches[3])) {
                return $this->unescape($matches[0]);
            }

            return $this->resolveString($matches[0]);

        }, $string);
    }

    /**
     * escape
     *
     * @param mixed $value
     *
     * @api
     *
     * @return mixed
     */
    public function escape($value)
    {
        return $this->doEscape($value, 0);
    }

    /**
     * unescape
     *
     * @param mixed $str
     *
     * @api
     *
     * @return mixed
     */
    public function unescape($value)
    {
        return $this->doEscape($value, 1);
    }

    /**
     * Sets the internal resolved state to unresolved and deletes all resolved
     * parmeters.
     *
     * Be careful with this. This method is internaly used when a parameter
     * collection is merged with another one.
     *
     * @internal
     *
     * @return mixed
     */
    public function setUnresolved()
    {
        $this->resolvedParams = [];
        $this->resolved = false;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($param)
    {
        return $this->has($param);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($param)
    {
        return $this->remove($param);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($param, $value)
    {
        return $this->set($param, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Selene\Module\DI\Exception\ParameterNotFoundException
     * @return mixed
     */
    public function offsetGet($param)
    {
        return $this->get($param);
    }

    /**
     * Return a reference to the current used parameters.
     *
     * @return array Reference to $this->resolvedParams or $this->parameters
     */
    private function &getParameters()
    {
        if ($this->resolved) {
            return $this->resolvedParams;
        }

        return $this->parameters;
    }

    /**
     * doResolve
     *
     * @return void
     */
    private function doResolve()
    {
        $resolved = [];

        foreach ($this->parameters as $key => $value) {
            if (is_array($value) || is_string($value)) {
                $resolved[$key] = $this->resolveParam($value);
            } else {
                $resolved[$key] = $value;
            }
        }

        $this->resolved = true;
        $this->resolvedParams = $resolved;
    }

    /**
     * doEscape
     *
     * @param mixed $value
     * @param int $method
     *
     * @return string|array
     */
    private function doEscape($value, $method = 0)
    {
        if (is_string($value)) {
            $seq = [
                self::$leftEscDelim.self::$leftDelim  => self::$leftDelim,
                self::$rightEscDelim.self::$rightDelim => self::$rightDelim
            ];

            return strtr($value, 0 === $method ? array_flip($seq) : $seq);
        }

        if (is_array($value) || $value instanceof \Traversable) {
            $result = [];

            foreach ($value as $key => $val) {
                $result[$key] = 0 === $method ? $this->escape($val) : $this->unescape($val);
            }

            return $result;
        }

        return $value;
    }

    /**
     * getMatchExp
     *
     * @return string
     */
    private function getMatchExp()
    {
        return sprintf(
            '~^%1$s([^%1$s\s%2$s$]+)%2$s$~',
            preg_quote(self::$leftDelim, '~'),
            preg_quote(self::$rightDelim, '~')
        );
    }

    /**
     * getReplaceExp
     *
     * @return string
     */
    private function getReplaceExp()
    {
        // 1: left delim escaper
        // 2: left delim
        // 3: right delim escaper
        // 4: right delim

        return sprintf(
            '~(%1$s?)%2$s([^%2$s\s%4$s$]+)(%3$s?)%4$s~',
            preg_quote(self::$leftEscDelim, '~'),
            preg_quote(self::$leftDelim, '~'),
            preg_quote(self::$rightEscDelim, '~'),
            preg_quote(self::$rightDelim, '~')
        );
    }
}
