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

use Serializable;
use Lucid\Mux\Parser\Variable;
use Lucid\Mux\Parser\TokenInterface;
use Lucid\Mux\Parser\ParserInterface;

/**
 * @class RouteContext
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteContext implements RouteContextInterface, Serializable
{
    /** @var string */
    private $staticPath;

    /** @var string */
    private $regex;

    /** @var array */
    private $vars;

    /** @var array */
    private $tokens;

    /** @var string */
    private $hostRegex;

    /** @var array */
    private $hostVars;

    /** @var array */
    private $hostTokens;

    /**
     * Construtor.
     *
     * @param string $staticPath
     * @param string $regex
     * @param array  $vars
     * @param string $hostRegex
     * @param array  $hostVars
     */
    public function __construct($staticPath, $regex, array $tokens = [], $hostRegex = null, array $hostTokens = [])
    {
        $this->staticPath = $staticPath;
        $this->regex      = $regex;
        $this->tokens     = $tokens;
        $this->hostRegex  = $hostRegex;
        $this->hostTokens = $hostTokens;

        $this->vars = $this->filterVars($tokens);
        $this->hostVars = $this->filterVars($hostTokens);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegex($raw = false)
    {
        return $raw ? $this->regex : self::wrapRegex($this->regex);
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticPath()
    {
        return $this->staticPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getHostTokens()
    {
        return $this->tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * {@inheritdoc}
     */
    public function getHostRegex($raw = false)
    {
        return $raw ? $this->hostRegex : self::wrapRegex($this->hostRegex);
    }

    /**
     * {@inheritdoc}
     */
    public function getHostVars()
    {
        return $this->hostParams;
    }

    /**
     * serialize
     *
     * @return string
     */
    public function serialize()
    {
        return serialize([
            'static_path' => $this->staticPath,
            'regex'       => $this->regex,
            'tokens'      => $this->tokens,
            'vars'        => $this->vars,
            'host_regex'  => $this->hostRegex,
            'host_vars'   => $this->hostVars,
            'host_tokens' => $this->hostTokens
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

        $this->staticPath = $data['static_path'];
        $this->regex      = $data['regex'];
        $this->tokens     = $data['tokens'];
        $this->vars       = $data['vars'];
        $this->hostRegex  = $data['host_regex'];
        $this->hostVars   = $data['host_vars'];
        $this->hostTokens = $data['host_tokens'];
    }

    /**
     * wrapRegex
     *
     * @param string $regex
     *
     * @return string
     */
    private static function wrapRegex($regex)
    {
        return sprintf('%1$s^%2$s$%1$ss', ParserInterface::EXP_DELIM, $regex);
    }

    private function filterVars(array $tokens)
    {
        return array_values(array_map(function (Variable $token) {
            return $token->value;
        }, array_filter($tokens, function (TokenInterface $token) {
            return $token instanceof Variable;
        })));
    }
}
