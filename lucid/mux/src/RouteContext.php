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

use Lucid\Mux\Parser\VariableInterface;
use Serializable;
use Lucid\Mux\Parser\Variable;
use Lucid\Mux\Parser\TokenInterface;
use Lucid\Mux\Parser\ParserInterface;

/**
 * @class RouteContext
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class RouteContext implements RouteContextInterface, Serializable
{
    /** @var string */
    private $staticPath;

    /** @var string */
    private $regex;

    /** @var array */
    private $vars;

    /** @var TokenInterface[] */
    private $tokens;

    /** @var string */
    private $hostRegex;

    /** @var array */
    private $hostVars;

    /** @var TokenInterface[] */
    private $hostTokens;

    /**
     * RouteContext constructor.
     *
     * @param string $staticPath
     * @param string $regex
     * @param TokenInterface[] $tokens
     * @param string|null $hostRegex
     * @param TokenInterface[] $hostTokens
     */
    public function __construct(
        string $staticPath,
        string $regex,
        array $tokens = [],
        string $hostRegex = null,
        array $hostTokens = []
    ) {
        $this->staticPath = $staticPath;
        $this->regex      = $regex;
        $this->hostRegex  = $hostRegex;

        $this->doSetTokens(...$tokens);
        $this->doSetHostTokens(...$hostTokens);
        $this->vars       = $this->filterVars(...$this->tokens);
        $this->hostVars   = $this->filterVars(...$this->hostTokens);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegex(bool $raw = false) : string
    {
        return $raw ? $this->regex : $this->wrapRegex($this->regex);
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticPath() : string
    {
        return $this->staticPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokens() : array
    {
        return $this->tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getHostTokens() : array
    {
        return $this->tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getVars() : array
    {
        return $this->vars;
    }

    /**
     * {@inheritdoc}
     */
    public function getHostRegex(bool $raw = false) : string
    {
        return $raw ? $this->hostRegex : $this->wrapRegex($this->hostRegex);
    }

    /**
     * {@inheritdoc}
     */
    public function getHostVars() : array
    {
        return $this->hostVars;
    }

    /**
     * serialize
     *
     * @return string
     */
    public function serialize() : string
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
     * @param string $data
     */
    public function unserialize($data) : void
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
    private function wrapRegex(string $regex) : string
    {
        return sprintf('%1$s^%2$s$%1$ss', ParserInterface::EXP_DELIM, $regex);
    }

    /**
     * @param \Lucid\Mux\Parser\TokenInterface[] ...$tokens
     */
    private function doSetTokens(TokenInterface ...$tokens) : void
    {
        $this->tokens = $tokens;
    }

    /**
     * @param \Lucid\Mux\Parser\TokenInterface[] ...$tokens
     */
    private function doSetHostTokens(TokenInterface ...$tokens) : void
    {
        $this->hostTokens = $tokens;
    }

    /**
     * @param \Lucid\Mux\Parser\TokenInterface[] ...$tokens
     *
     * @return array
     */
    private function filterVars(TokenInterface ...$tokens) : array
    {
        return array_values(array_map(function (VariableInterface $token) {
            return $token->value();
        }, array_filter($tokens, function (TokenInterface $token) {
            return $token instanceof VariableInterface;
        })));
    }
}
