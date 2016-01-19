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
use Lucid\Mux\Exception\ParserException;
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
(?:
        (?P<ldel>%1$s)?
    \{
        (?P<var>\w+)
    \??\}
        (?P<rdel>%1$s)?
)
REGEX;

    /** @var string */
    const L_DELIM = '{';

    /** @var string */
    const R_DELIM = '}';

    /** @var string */
    const OPTQ    = '?';

    /** @var string */
    const K_VAR   = 'var';

    /** @var string */
    const K_OPT   = 'opt';

    /** @var string */
    const N_MGRP  = '(?P<%s>%s)';

    /** @var string */
    const U_MGRP  = '(?:%s%s)';

    /**
     * Parses a route object.
     *
     * @param RouteInterface $route
     *
     * @return RouteContextInterface
     */
    public static function parse(RouteInterface $route)
    {
        extract(self::transpilePattern($route->getPattern(), false, $route->getConstraints(), $route->getDefaults()));

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
    public static function transpilePattern($pattern, $host = false, array $requirements = [], array $defaults = [])
    {
        $tokens = self::tokenizePattern($pattern, $host, $requirements, $defaults);

        $staticPath = $tokens[0]->isText() ? $tokens[0]->getValue() : '';

        $vars = array_map(function (TokenInterface $token) {
            return $token->getValue();
        }, array_filter($tokens, function (TokenInterface $token) {
            return $token->isVariable();
        }));

        $regex = self::transpileMatchRegex($tokens);

        return self::getCompact($staticPath, $regex, $vars, $tokens);
    }

    public static function tokenizePattern($pattern, $isHost = false, array $requirements = [], array $defaults = [])
    {
        // left pad pattern with separator
        if (!$isHost && false === self::isSeparator(mb_substr($pattern, 0, 1))) {
            $pattern = '/'.$pattern;
        }

        list ($staticPath,) = $splt = array_filter(preg_split(
            '~(\{(.*?)\})~',
            $pattern
        ), function ($str) {
            return 0 !== strlen($str);
        });

        $match_del = join('|', array_map(function ($sign) {
            return preg_quote($sign, Ps::EXP_DELIM);
        }, str_split(Ps::SEPARATORS)));

        $expr = Ps::EXP_DELIM.sprintf(self::VAR_REGEXP, $match_del).Ps::EXP_DELIM.'x';

        preg_match_all($expr, $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        $pos    = 0;
        $plen   = strlen($pattern);
        $nlen   = count($matches) - 1;
        $tokens = [];

        //var_dump($matches);
        //die;

        foreach ($matches as $i => $match) {
            $ldel       = -1 === $match['ldel'][1] ? null : $match['ldel'];
            $var        = -1 === $match['var'][1] ? null : $match['var'];
            $opt        = 0 === strcmp('?', substr($match[0][0], -2, 1));

            if (isset($match['rdel'])) { 
                $rdel       = -1 === $match['rdel'][1] ? null : $match['rdel'];
            } else {
                $rdel = null;
            }

            $offset     = $match[0][1];

            $default    = isset($defaults[$var[0]]) ? $defaults[$var[0]] : null;
            $constraint = isset($requirements[$var[0]]) ? $requirements[$var[0]] : null;

            // if an optional variable is followed by something and the
            // variable has no default value assigned, throw and exception:
            //if ($pos < $plen && $opt && (null === $default && null === $constraint)) {
            //    throw ParserException::nestedOptional($var[0]);
            //}

            if (0 !== strlen($str = substr($pattern, $pos, $offset - $pos))) {
                $text = new Text($str, $lt = self::lastToken($tokens));
                self::pushTokens($text, $lt, $tokens);
            }

            if (null !== $ldel) {
                $delim = new Delimiter($ldel[0], $lt = self::lastToken($tokens));
                self::pushTokens($delim, $lt, $tokens);
            }

            $tVar = new Variable($var[0], !$opt, null, $lt = self::lastToken($tokens));
            self::pushTokens($tVar, $lt, $tokens);

            if (null !== $rdel) {
                $delim = new Delimiter($rdel[0], $lt = self::lastToken($tokens));
                self::pushTokens($delim, $lt, $tokens);
            }

            $pos = $offset + strlen($match[0][0]);

            if ($nlen === $i && $plen !== $pos) {
                $tail = substr($pattern, -($plen - $pos));

                if (self::isSeparator($edl = substr($tail, 0, 1))) {
                    $tail = substr($tail, 1);

                    $d = new Delimiter($edl, $lt = self::lastToken($tokens));
                    self::pushTokens($d, $lt, $tokens);
                }

                if (0 !== strlen($tail)) {
                    $t = new Text($tail, $lt = self::lastToken($tokens));
                    self::pushTokens($t, $lt, $tokens);
                }
            }
        }

        return $tokens;
    }

    /**
     * isSeparator
     *
     * @param string $test
     *
     * @return bool
     */
    public static function isSeparator($test)
    {
        return 1 === strlen($test) && false !== strpos(Ps::SEPARATORS, $test);
    }

    /**
     * Transpiles tokens to a regex.
     *
     * @param array $tokens
     *
     * @return string
     */
    public static function transpileMatchRegex(array $tokens)
    {
        $regex = [];

        foreach ($tokens as $token) {
            $var = $token instanceof Variable ? $token : ($token instanceof Delimiter ? $token->next : null);

            if (null !== $var && $var instanceof Variable && null !== ($optgrp = self::makeOptGrp($var))) {
                $regex[] = $optgrp;
                break;
            }

            $regex[] = $token;
        }

        return implode('', $regex);
    }

    /**
     * @param Variable $var
     *
     * @return string|null
     */
    private static function makeOptGrp(Variable $var)
    {
        $opt = false;

        if ($var->required || $var->next instanceof Text) {
            return null;
        }

        if (null === $var->next) {
            $optgrp = '';
        } elseif ($vn = self::findNext($var)) {
            $optgrp = self::makeOptGrp($vn);
        } else {
            return null;
        }

        $p = $var->prev instanceof Delimiter ? $var->prev : '';

        return sprintf('(?:%s%s%s)?', $p, $var, $optgrp); 
    }

    /**
     * @param Variable $t
     *
     * @return Variable|null
     */
    private static function findNext(Variable $t) {
        if (null === ($n = $t->next) || !($n instanceof Variable)) {
            return;
        }

        return $n;
    }

    private static function pushTokens(TokenInterface $token, TokenInterface $prev = null, array &$tokens = [])
    {
        if (null !== $prev) {
            $prev->next = $token;
        }

        $token->prev = $prev;

        $tokens[] = $token;
    }


    /**
     * lastToken
     *
     * @param array $tokens
     *
     * @return TokenInterface
     */
    private static function lastToken(array $tokens)
    {
        if ($token = end($tokens)) {
            return $token;
        }

        return null;
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
        if (null === $host = $route->getHost()) {
            return [];
        }

        return self::transpilePattern($host, true, $route->getConstraints(), $route->getDefaults());
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
