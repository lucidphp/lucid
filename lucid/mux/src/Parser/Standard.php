<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Parser;

use Lucid\Mux\RouteContext;
use Lucid\Mux\RouteInterface;
use Lucid\Mux\RouteContextInterface as ContextInterface;
use InvalidArgumentException;
use Lucid\Mux\Parser\ParserInterface as Ps;

/**
 * @class Default
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class Standard implements ParserInterface
{
    /** @var string */
    const VAR_REGEXP = <<<'REGEX'
# start first alternative, none-capturing group
(?: \{
# Named capturing group for matching optionals
    (?P<%s>\w+)
\?\} )
    |
# start second alternative, none-capturing group
(?: \{
# Named capturing group for matching required variables
    (?P<%s>\w+)
\} )
REGEX;

    /** @var string */
    const SPLIT_REGEXP = '\{(.*?)\}';

    /** @var string */
    const L_DELIM      = '{';

    /** @var string */
    const R_DELIM      = '}';

    /** @var string */
    const OPTQ         = '?';

    /** @var string */
    const K_VAR        = 'var';

    /** @var string */
    const K_OPT        = 'opt';

    /** @var string */
    const REQUIREMENTS = '[^%s%s]+';

    /** @var string */
    const N_MGRP       =  '(?P<%s>%s)';

    /** @var string */
    const NMGRP        = '(?:%s%s)?';

    /**
     * {@inheritdoc}
     */
    public static function parse(RouteInterface $route)
    {
        extract(self::transpilePattern($route, $route->getPattern(), false));

        return new RouteContext($staticPath, $expression, $vars, $tokens, self::parseHostVars($route));
    }

    /**
     * compilePatter
     *
     * @param RouteInterface $route
     * @param string $pattern
     * @param bool $isHost
     *
     * @return array
     */
    public static function transpilePattern(RouteInterface $route, $pattern, $isHost = false)
    {
        $expr   = Ps::EXP_DELIM.sprintf(self::VAR_REGEXP, self::K_OPT, self::K_VAR).Ps::EXP_DELIM.'x';

        list ($staticPath,) = $splt = preg_split($expr, $pattern, PREG_SPLIT_NO_EMPTY);

        if (!preg_match_all($expr, $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            return self::getCompact($staticPath, preg_quote($staticPath, Ps::EXP_DELIM));
        }

        $separator = $isHost ? '.' : '/';
        $vars      = [];
        $tokens    = [];
        $lastopt   = [];
        $pos       = 0;

        $i         = 0;
        $count     = count($matches);
        $len       = mb_strlen($pattern);

        for (; $i < $count; $i++) {
            $grp = $matches[$i];

            if (null === $key = isset($grp[self::K_VAR]) ?
                self::K_VAR : (isset($grp[self::K_OPT]) ? self::K_OPT : null)) {
                continue;
            }

            $default = $route->getDefault($name = $grp[$key][0]);
            $optional = self::K_OPT === $key;

            $match  = $grp[0][0];
            $offset = $grp[0][1];

            $end = mb_strlen($match) + $offset;

            // if an optional variable is followed by something and the
            // variable has no default value assigned, throw and exception:
            if ($end < $len && $optional && null === $default) {
                throw new \LogicException('BOO BOO');
            }

            $vars[] = $name;


            if ($text = self::createTextToken($pattern, $offset, $pos)) {
                $tokens[] = $text;
            }

            $tokens[] = self::createVariableToken(
                $name,
                $pattern,
                $separator,
                $route->getConstraint($name),
                !$optional,
                $pos,
                $end
            );

            // if there's a trailing static path:
            if ($i === ($count - 1) && null !== array_pop($splt)) {

                if ($text = self::createTextToken($pattern, $len, $end)) {
                    $tokens[] = $text;
                }
            }

            $pos = $end;
        }

        return self::getCompact($staticPath, self::transpileMatchRegex($tokens), $vars, $tokens);
    }

    /**
     * transpileMatchRegex
     *
     * @param array $tokens `TokenInterface[]`
     *
     * @return string
     */
    private static function transpileMatchRegex(array $tokens)
    {
        $tlen = count($tokens);

        /** @var \Lucid\Mux\Parser\TokenInterface */
        $t0 = $tokens[0];

        // if theres only one variable token
        // close the expression
        if (1 === $tlen && $t0->isVariable() && $t0->isRequired()) {
            return sprintf('%s'.self::N_MGRP.'?', preg_quote($t0->getSeparator()), $t0->getValue(), $t0->getRegexp());
        }

        $regexp = '';

        while ($tn = array_shift($tokens)) {

            if ($tn->isText()) {
                $regexp .= preg_quote($tn->getValue(), Ps::EXP_DELIM);
            } else {
                $regexp .= self::getVariableTokenRegexp($tn, $tokens);
            }
        }

        return $regexp;
    }

    /**
     * getVariableTokenRegexp
     *
     * @param array $token
     * @param array $tokens
     *
     * @return mixed
     */
    private static function getVariableTokenRegexp(TokenInterface $token, array $tokens)
    {
        $separator = $token->getSeparator();

        // find the next optional param and check if it can be optional
        if ($optional = !$token->isRequired()) {
            $optional = true;

            foreach ($tokens as $tn) {
                if ($tn->isText() || !$tn->isRequired()) {
                    $optional = false;
                    break;
                }
            }
        }

        if ($optional) {
            return self::getOptionalTokenRegexp($token, $tokens);
        }

        return sprintf(
            '%s%s',
            preg_quote($separator, Ps::EXP_DELIM),
            sprintf(self::N_MGRP, $token->getValue(), $token->getRegexp())
        );
    }

    /**
     * getOptionalTokenRegexp
     *
     * @param mixed $token
     * @param array $tokens
     * @param mixed $expression
     *
     * @return string
     */
    private static function getOptionalTokenRegexp(TokenInterface $token, array $tokens)
    {
        array_unshift($tokens, $token);

        $option = '';

        while (!empty($tokens)) {
            $token = array_pop($tokens);
            $option = sprintf(
                self::NMGRP,
                preg_quote($token[1]),
                sprintf(self::N_MGRP, $token[3], $token[2]).$option
            );
        }

        return $option;
    }

    /**
     * createVariableToken
     *
     * @param string $name
     * @param string $pattern
     * @param string $delim
     * @param string $regex
     * @param bool   $required
     * @param int    $start
     * @param int    $end
     *
     * @return TokenInterface
     */
    private static function createVariableToken($name, $pattern, $delim, $regex, $required, $start = 0, $end = 0)
    {
        // if there's no requirement for a wildcard parameter we have to
        // build our own.
        if (null === $regex) {
            $regex = self::transpileVariableRegex($name, $pattern, $delim, $start, $end);
        }

        return new Token([Token::T_VARIABLE, '', $regex, $name, $required]);
    }

    /**
     * transpileVariableRegex
     *
     * @param string $name
     * @param string $pattern
     * @param string $currentSp
     * @param int    $pos
     * @param int    $end
     *
     * @return void
     */
    private static function transpileVariableRegex($name, $pattern, $currentSp, $pos, $end)
    {
        $tail = mb_substr($pattern, $end);

        $nextSp = (0 === mb_strlen($tail) || 0 === mb_strpos($tail, Ps::EXP_DELIM)) ?
            Ps::EXP_DELIM :
            (isset($tail[0]) ? $tail[0] : Ps::EXP_DELIM);

        $regexp = sprintf(
            self::REQUIREMENTS,
            preg_quote($currentSp, Ps::EXP_DELIM),
            preg_quote($nextSp === $currentSp ? '' : $nextSp, Ps::EXP_DELIM)
        );

        if (0 !== mb_strlen($nextSp) || 0 === (mb_strlen($pattern) - $end)) {
            $regexp .= '+';
        }

        return $regexp;
    }

    /**
     * Returns a text token
     *
     * @param string $pattern the route pattern.
     * @param int    $start   the start position of the token
     * @param int    $pos     the current position
     *
     * @return TokenInterface returns a text token or `null`.
     */
    private static function createTextToken($pattern, $start, $pos)
    {
        if (false === (bool)$char = mb_substr($text = mb_substr($pattern, $pos, $start - $pos), - 1)) {
            return;
        }

        $isSeparator = false !== mb_strpos(Ps::SEPARATORS, $char);

        if ($isSeparator && mb_strlen($text) > 1) {
            return new Token([Token::T_TEXT, substr($text, 0, -1)]);
        }

        if (!$isSeparator && mb_strlen($text) > 0) {
            return new Token([Token::T_TEXT, $text]);
        }
    }

    /**
     * parseHostVars
     *
     * @param RouteInterface $route
     *
     * @return array
     */
    private static function parseHostVars(RouteInterface $route)
    {
        if (null !== ($host = $route->getHost())) {
            $results = self::compilePattern($route, $host, true);

            return [
                'parameters' => $results['parameters'],
                'expression' => $results['expression'],
                'tokens'     => $results['tokens']
            ];
        }

        return [];
    }

    /**
     * getCompact
     *
     * @param string $staticPath
     * @param string $expression
     * @param array $vars
     * @param array $tokens
     *
     * @return array
     */
    private static function getCompact($staticPath, $expression, array $vars = [], array $tokens = [])
    {
        return compact('staticPath', 'expression', 'vars', 'tokens');
    }
}
