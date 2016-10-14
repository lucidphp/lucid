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
use Lucid\Mux\Parser\ParserInterface as Ps;
use Lucid\Mux\RouteContextInterface as ContextInterface;

/**
 * @class Standard
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class Standard
{
    /** @var string */
    const VAR_REGEXP = <<<'REGEX'
(?:
    (?P<ldel>%1$s)?
    %2$s
    (?P<var>\w+)
    \??%3$s
    (?P<rdel>%1$s)?
)
REGEX;

    /** @var string */
    const L_DELIM = '{';

    /** @var string */
    const R_DELIM = '}';

    /**
     * Parses a route object.
     *
     * @param RouteInterface $route
     *
     * @return ContextInterface
     */
    public static function parse(RouteInterface $route) : ContextInterface
    {
        extract(self::transpilePattern($route->getPattern(), false, $route->getConstraints(), $route->getDefaults()));
        $host = self::parseHostVars($route);

        return new RouteContext($staticPath, $expression, $tokens, $host['expression'], $host['tokens']);
    }

    /**
     * Transpiles the the given pattern into a useful format.
     *
     * @param string $pattern
     * @param bool $host
     * @param array $requirements
     * @param array $defaults
     *
     * @return array
     */
    public static function transpilePattern(
        string $pattern,
        bool $host = false,
        array $requirements = [],
        array $defaults = []
    ) : array {
        $tokens     = self::tokenizePattern($pattern, $host, $requirements, $defaults);
        $staticPath = !$tokens[0] instanceof Variable ? $tokens[0]->value : '/';

        return self::getCompact($staticPath, self::transpileMatchRegex($tokens), $tokens);
    }

    /**
     * tokenizePattern
     *
     * @param mixed $pattern
     * @param mixed $isHost
     * @param array $requirements
     * @param array $defaults
     *
     * @return TokenInterface[]
     */
    public static function tokenizePattern(
        string $pattern,
        bool $isHost = false,
        array $requirements = [],
        array $defaults = []
    ) : array {
        // left pad pattern with separator
        if (!$isHost && false === self::isSeparator(substr($pattern, 0, 1))) {
            $pattern = '/'.$pattern;
        }

        list ($path, ) = $splt = array_pad(preg_split('#\{\w+\??\}#', $pattern), 1, '/');

        if (2 > count($splt)) {
            return [new Text($path)];
        }

        $separator = $isHost ? '.' : '/';
        $matchDel = join('|', array_map('preg_quote', str_split(Ps::SEPARATORS), str_split(
            str_repeat(Ps::EXP_DELIM, strlen(Ps::SEPARATORS))
        )));

        $expr = Ps::EXP_DELIM.sprintf(
            self::VAR_REGEXP,
            $matchDel,
            preg_quote(self::L_DELIM, Ps::EXP_DELIM),
            preg_quote(self::R_DELIM, Ps::EXP_DELIM)
        ).Ps::EXP_DELIM.'x';

        preg_match_all($expr, $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        $pos    = 0;
        $plen   = strlen($pattern);
        $nlen   = count($matches) - 1;
        $tokens = [];

        foreach ($matches as $i => $match) {
            // right hand delimiter, not always present
            $rdel = (!isset($match['rdel']) || -1 === $match['rdel'][1]) ? null : $match['rdel'];
            $ldel = -1 === $match['ldel'][1] ? null : $match['ldel'];
            $var  = -1 === $match['var'][1] ? null : $match['var'];
            $opt  = 0 === strcmp('?', substr($match[0][0], -2, 1));

            $default    = isset($defaults[$var[0]]) ? $defaults[$var[0]] : null;
            $constraint = isset($requirements[$var[0]]) ? $requirements[$var[0]] : null;

            $offset     = $match[0][1];

            // add preceeding text
            if (0 !== strlen($str = substr($pattern, $pos, $offset - $pos))) {
                $text = new Text($str, $lt = self::lastToken($tokens));
                self::pushTokens($text, $lt, $tokens);
            }

            // add left hand delimiter
            if (null !== $ldel) {
                $delim = new Delimiter($ldel[0], $lt = self::lastToken($tokens));
                self::pushTokens($delim, $lt, $tokens);
            }

            $opt = null === $default ? $opt : true;

            // add variable
            $tVar = new Variable($var[0], !$opt, $constraint, $lt = self::lastToken($tokens), null, $separator);
            self::pushTokens($tVar, $lt, $tokens);

            // add right hand delimiter
            if (null !== $rdel) {
                $delim = new Delimiter($rdel[0], $lt = self::lastToken($tokens));
                self::pushTokens($delim, $lt, $tokens);
            }

            // update current position
            $pos = $offset + strlen($match[0][0]);

            // add trailing text.
            if ($nlen === $i && $plen !== $pos) {
                $tail = substr($pattern, -($plen - $pos));

                if (0 !== strlen($tail)) {
                    $t = new Text($tail, $lt = self::lastToken($tokens));
                    self::pushTokens($t, $lt, $tokens);
                }
            }
        }

        return $tokens;
    }

    /**
     * Checks if given string is a url delimiter.
     *
     * @param string $test
     *
     * @return bool
     */
    public static function isSeparator(string $test) : bool
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
    public static function transpileMatchRegex(array $tokens) : string
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
     * Recursively iterates over tailing optional variables.
     *
     * @param Variable $var
     *
     * @return string will reuturn `null` if no matches are found.
     */
    private static function makeOptGrp(Variable $var) : ?string
    {
        if ($var->required) {
            return null;
        }

        list ($next, $nextIsOpt) = self::findNextOpt($var);

        if (!$nextIsOpt) {
            return null;
        }

        //$nextOpt = null !== $next ? self::makeOptGrp($next) : null;

        //if (null === $nextOpt && null !== $next) {
        //    return;
        //}

        $optgrp = null !== $next ? self::makeOptGrp($next) : '';
        $p      = $var->prev instanceof Delimiter ? $var->prev : '';

        return sprintf('(?:%s%s%s)?', $p, $var, $optgrp);
    }

    /**
     * Finds next optional valiable token
     *
     * @param Variable $var
     *
     * @return array
     */
    private static function findNextOpt(Variable $var)
    {
        $nextIsOpt = true;

        $next = null;
        /** @var Token $n */
        $n = $var->next;

        while (null !== $n) {

            if (!$n instanceof Variable) {
                $n = $n->next;
                $nextIsOpt = true;
                continue;
            }

            if (!$n->required) {
                $nextIsOpt = true;
                $next = $n;
                break;
            }

            $nextIsOpt = false;
            $n = $n->next;
        }

        return [$next, $nextIsOpt];
    }

    /**
     * pushTokens
     *
     * @param TokenInterface $token
     * @param TokenInterface $prev
     * @param array $tokens
     *
     * @return void
     */
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
            return ['expression' => null, 'tokens' => []];
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
    private static function getCompact($staticPath, $expression, array $tokens = [])
    {
        return compact('staticPath', 'expression', 'tokens');
    }
}
