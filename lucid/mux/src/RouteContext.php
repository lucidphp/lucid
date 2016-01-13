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
use Lucid\Mux\Parser\ParserInterface;

/**
 * @class RouteExpression
 *
 * @package lucid/routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteContext implements RouteContextInterface, Serializable
{
    private $staticPath;
    private $parameters;
    private $tokens;
    private $regexp;
    private $hostExp;
    private $hostParams;
    private $hostTokens;

    /**
     * Constructor
     *
     * @param string $sPath
     * @param string $regexp
     * @param array $params
     * @param array $tokens
     * @param string $hostExp
     * @param array $hostParmas
     * @param array $hostTokens
     *
     * @return void
     */
    public function __construct($sPath, $regexp, array $params, array $tokens, array $host = [])
    {
        $this->staticPath = $sPath;
        $this->regexp = $regexp;
        $this->parameters = $params;
        $this->tokens = $tokens;

        $this->setHost($host);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegexp($raw = false)
    {
        return $raw ? $this->regexp : self::wrapRegexp($this->regexp);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
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
    public function getStaticPath()
    {
        return $this->staticPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getHostRegexp($raw = false)
    {
        return $raw ? $this->hostExp : self::wrapRegexp($this->hostExp);
    }

    /**
     * {@inheritdoc}
     */
    public function getHostParameters()
    {
        return $this->hostParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getHostTokens()
    {
        return $this->hostTokens;
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
            'regexp'      => $this->regexp,
            'parameters'  => $this->parameters,
            'tokens'      => $this->tokens,
            'host_exp'    => $this->hostExp,
            'host_params' => $this->hostParams,
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
        $this->staticPath  = $data['static_path'];
        $this->regexp      = $data['regexp'];
        $this->parameters  = $data['parameters'];
        $this->tokens      = $data['tokens'];
        $this->hostExp     = $data['host_exp'];
        $this->hostParams  = $data['host_params'];
        $this->hostTokens  = $data['host_tokens'];
    }

    private function setHost(array $host)
    {
        $host = array_merge(['parameters' => [], 'expression' => null, 'tokens' => []], $host);

        $this->hostParams = $host['parameters'];
        $this->hostExp    = $host['expression'];
        $this->hostTokens = $host['tokens'];
    }

    /**
     * wrapRegexp
     *
     * @param string $regexp
     *
     * @return string
     */
    private static function wrapRegexp($regexp)
    {
        return sprintf('%1$s^%2$s$%1$ss', ParserInterface::EXP_DELIM, $exp);
    }
}
