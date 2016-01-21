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
use Lucid\Mux\Parser\Text;

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

    /** @var string */
    private $hostRegex;

    /** @var array */
    private $hostVars;

    /**
     * Construtor.
     *
     * @param string $staticPath
     * @param string $regex
     * @param array  $vars
     * @param string $hostRegex
     * @param array  $hostVars
     */
    public function __construct($staticPath, $regex, array $vars = [], $hostRegex = null, array $hostVars = [])
    {
        $this->staticPath = $staticPath;
        $this->regex      = $regex;
        $this->vars       = $vars;
        $this->hostVars   = $hostVars;
        $this->hostRegex  = $hostRegex;
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
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * {@inheritdoc}
     */
    public function getHostRegex($raw = false)
    {
        return $raw ? $this->hostExp : self::wrapRegex($this->hostExp);
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
            'vars'        => $this->vars,
            'host_regex'  => $this->hostRegex,
            'host_vars'   => $this->hostVars
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
        $this->parameters = $data['vars'];
        $this->hostRegex  = $data['host_regex'];
        $this->hostVars   = $data['host_vars'];
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
}
