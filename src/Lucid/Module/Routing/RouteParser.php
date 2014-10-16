<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing;

/**
 * @class RouteCompiler
 * @package Selene\Module\Routing
 * @version $Id$
 */
class RouteParser
{
    const PARAM_LDELIM = '{';

    const PARAM_RDELIM = '}';

    const SEPARATORS = '/.;:-_~+*=|';

    const PATTERN_DELIM = '#';

    const REQUIREMENTS = '[^%s%s]+';

    const VAR_GRP =  '(?P<%s>%s)';

    const OPT_GRP = '(?:%s%s)?';

    const MATCH_REGEXP = '\%s(\w+)(\?)?\%s';

    /**
     * static::compile
     *
     * @param Route $route
     *
     * @access public
     * @return void
     */
    public static function parse(Route $route)
    {
        $compiled = [];

        if (null !== ($host = $route->getHost())) {
            $results = static::compilePattern($route, $host, true);

            $compiled['host'] = [
                'vars'   => $results['parameters'],
                'regexp' => $results['expression'],
                'tokens' => $results['tokens'],
            ];
        }

        $results = static::compilePattern($route, $route->getPattern(), false);

        return new RouteContext(
            $results['staticPath'],
            $results['expression'],
            $results['parameters'],
            $results['tokens'],
            isset($compiled['host']['expression']) ? $compiled['host']['expression'] : null,
            isset($compiled['host']['parameters']) ? $compiled['host']['parameters'] : [],
            isset($compiled['host']['tokens']) ? $compiled['host']['tokens'] : []
        );
    }

    /**
     * Compile the regex pattern for a route.
     *
     * @access protected
     * @return array
     */
    private static function compilePattern(Route $route, $pattern, $host)
    {
        $matches = [];
        $parameters = [];
        $tokens = [];
        $cursor = 0;
        $expression = '^';
        $patternLen = strlen($pattern);
        $tail = null;

        $currentSeparator = $separator = $host ? '.' : '/';

        if (false !== ($pos = strpos($pattern, static::PARAM_LDELIM))) {
            $matchRegexp = static::PATTERN_DELIM.
                sprintf(static::MATCH_REGEXP, static::PARAM_LDELIM, static::PARAM_RDELIM).
                static::PATTERN_DELIM;

            preg_match_all($matchRegexp, $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            $staticPath = '/'.trim(substr($pattern, 0, $pos), '/');
            $tail = substr($pattern, strrpos($pattern, static::PARAM_RDELIM) + 1);
        } else {
            $staticPath = $pattern;
        }


        // match all first level options first
        foreach ($matches as $match) {

            $optional      = false;
            $parameters[]  = $paramName = static::validateParamMatch($route, $match, $parameters, $host, $optional);
            $endOffset     = $match[0][1] + strlen($match[0][0]);
            $offsetString  = substr($pattern, $cursor, $match[0][1] - $cursor);
            $offsetChar    = substr($offsetString, - 1);
            $isSeparator   = false !== strpos(static::SEPARATORS, $offsetChar);
            $cursor        = $endOffset;

            if ($isSeparator && strlen($offsetString) > 1) {
                $tokens[] = ['text', substr($offsetString, 0, -1)];
            } elseif (!$isSeparator && strlen($offsetString) > 0) {
                $tokens[] = ['text', $offsetString];
            }

            $regexp = static::getParamRegexp(
                $route,
                $pattern,
                $paramName,
                $cursor,
                $patternLen,
                $endOffset,
                $host,
                $currentSeparator
            );

            $tokens[] = ['variable', $offsetChar, $regexp, $paramName, $optional];
        }

        if (null !== $tail && 0 < strlen($tail)) {
            $tokens[] = ['text', rtrim($tail, static::SEPARATORS)];
        }

        if (empty($tokens)) {
            $tokens[] = ['text', $staticPath];
        }

        $expression = static::buildMatchRegexp($tokens);
        $tokens = array_reverse($tokens);

        return compact('expression', 'parameters', 'tokens', 'staticPath');
    }

    /**
     * Compute the regexp part for a host/route parameter.
     *
     * @param Route $route
     * @param mixed $pattern
     * @param mixed $paramName
     * @param mixed $cursor
     * @param mixed $patternLen
     *
     * @access private
     * @return mixed
     */
    private static function getParamRegexp(
        Route $route,
        $pattern,
        $paramName,
        $cursor,
        $patternLen,
        $endOffset,
        $host,
        $currentSeparator = '/'
    ) {
        // if there's no requirement for a wildcard parameter we have to
        // build our own.
        if (!$regexp = static::getRequirement($route, $paramName, $host)) {
            $remains   = substr($pattern, $cursor);

            $nextSeparator = (0 === strlen($remains) || 0 === strpos($remains, '{')) ?
                '' :
                (isset($remains[0]) ? $remains[0] : '');

            $regexp = sprintf(
                static::REQUIREMENTS,
                preg_quote($currentSeparator, '#'),
                preg_quote($nextSeparator === $currentSeparator ? '' : $nextSeparator, '#')
            );

            if (0 !== strlen($nextSeparator) || 0 === ($patternLen - $endOffset)) {
                $regexp .= '+';
            }

        }
        return $regexp;
    }

    /**
     * getRequirement
     *
     * @param Route $route
     * @param mixed $param
     * @param mixed $host
     *
     * @access private
     * @return mixed
     */
    private static function getRequirement(Route $route, $param, $host = false)
    {
        if (!$host) {
            return static::getPatternRequirement($route, $param);
        }

        return static::getHostRequirement($route, $param);
    }

    /**
     * getPatternRequirement
     *
     * @param Route $route
     * @param mixed $param
     *
     * @access private
     * @return mixed
     */
    private static function getPatternRequirement(Route $route, $param)
    {
        if ($req = $route->getConstraint($param, null, false)) {
            return $req;
        }

        return false;
    }

    /**
     * getHostRequirement
     *
     * @param Route $route
     * @param mixed $param
     *
     * @access private
     * @return mixed
     */
    private static function getHostRequirement(Route $route, $param)
    {
        if ($req = $route->getConstraint($param, null, false)) {
            return $req;
        }

        //$route->setConstraint($param, static::REQUIREMENTS);

        return false;
    }

    /**
     * Validate a route parameter.
     *
     * @param Route $route
     * @param array $match
     * @param array $parameters
     * @param mixed $host
     * @param mixed $optional
     *
     * @throws DomainException
     * @throws Selene\Module\Routing\Exception\RouteVariableException
     * @access private
     * @return string
     */
    private static function validateParamMatch(
        Route $route,
        array $match,
        array &$parameters,
        $host = false,
        &$optional = false
    ) {
        if (is_numeric($paramName = current($match[1]))) {
            throw new \DomainException(sprintf('%s is not a valid parameter', $paramName));
            break;
        }

        if (in_array($paramName, $parameters)) {
            throw new \InvalidArgumentException(sprintf('variable {%s} in a uri must not be repeated', $paramName));
            break;
        }

        //$optional = $route->isOptional($paramName) ? true : isset($match[2]);
        $optional = isset($match[2]) || (!$host && null !== $route->getDefault($paramName));

        if ($host && $optional) {
            throw new \DomainException(
                sprintf('%s host expression may not contain optional placeholders', $paramName)
            );
            break;
        }

        return $paramName;
    }

    /**
     * Builds the route/host regexp.
     *
     * @access private
     * @return string
     */
    private static function buildMatchRegexp(array $tokens)
    {
        $expression = '#^';
        $separator;
        $tokenLen = count($tokens);

        if (1 === $tokenLen && isset($tokens[0][4]) && $tokens[0][4]) {
            $expression .= sprintf(
                '%s'.static::VAR_GRP.'?',
                preg_quote($tokens[0][1]),
                $tokens[0][3],
                $tokens[0][2]
            );
        } else {
            $expression .= static::getMatchRegexp($tokens);
        }

        $expression .= '$#s';
        return $expression;
    }

    /**
     * buildMatchExpression
     *
     * @param array $tokens
     * @param string $expression
     *
     * @access private
     * @return mixed
     */
    private static function getMatchRegexp(array $tokens, $expression = '')
    {
        $separator;

        while (!empty($tokens)) {
            $token = array_shift($tokens);

            if ('text' === $token[0]) {
                $expression .= preg_quote($token[1], '#');
            } elseif ('variable' === $token[0]) {
                $expression .= static::getVariableTokenRegexp($token, $tokens);
            }
        }

        return $expression;
    }

    /**
     * getVariableTokenRegexp
     *
     * @param array $token
     * @param array $tokens
     *
     * @access private
     * @return mixed
     */
    private static function getVariableTokenRegexp(array $token, array &$tokens)
    {
        $separator = $token[1];

        // find the next optional param and check if it can be optional
        $optional = false;

        if ($token[4]) {
            $optional = true;

            foreach ($tokens as $tok) {
                if ('text' === $tok[0] || !$tok[4]) {
                    $optional = false;
                    break;
                }
            }
        }

        if ($optional) {
            return static::getOptionalTokenRegexp($token, $tokens);
        }

        return sprintf(
            '%s%s',
            preg_quote($separator, '#'),
            sprintf(static::VAR_GRP, $token[3], $token[2])
        );
    }

    /**
     * getOptionalTokenRegexp
     *
     * @param mixed $token
     * @param array $tokens
     * @param mixed $expression
     *
     * @access private
     * @return string
     */
    private static function getOptionalTokenRegexp($token, array &$tokens)
    {
        array_unshift($tokens, $token);

        $option = '';

        while (!empty($tokens)) {
            $token = array_pop($tokens);
            $option = sprintf(
                static::OPT_GRP,
                preg_quote($token[1]),
                sprintf(static::VAR_GRP, $token[3], $token[2]).$option
            );
        }

        return $option;
    }

    private function __construct()
    {
    }
}
