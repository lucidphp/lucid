<?php

/*
 * This File is part of the Lucid\Mux\Psr7 package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Psr7;

use Lucid\Mux\Router as BaseRouter;
use Lucid\Mux\RouterInterface as DefaultRouter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Lucid\Mux\Request\Context as RequestContext;
use Lucid\Mux\Matcher\ContextInterface as MatchContextInterface;

/**
 * @class Router
 *
 * @package Lucid\Mux\Psr7
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Router extends BaseRouter implements RouterInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request)
    {
        $response = $this->dispatchMatch($this->matchRequest($request));

        if (!$response instanceof ResponseInterface) {
            throw new \RuntimeException;
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(ServerRequestInterface $request)
    {
        $match = $this->match(RequestContext::fromPsrRequest($request));

        return $this->insertToMatch($match, $request);
    }

    /**
     * {@inheritdoc}
     */
    private function map(ServerRequestInterface $request, MatchContextInterface $match)
    {
        foreach ($match->getVars() as $attr => $value) {
            $request = $request->withAttribute($attr, $value);
        }

        return $request;
    }

    private function insertToMatch(MatchContextInterface $match, ServerRequestInterface $request)
    {
        $vars = $match->getVars();

        $fn = function ($match) use ($request) {

            if (array_key_exists('request', $match->vars)) {
                throw new \LogicException();
            }

            $vals = array_values($match->vars);
            $keys = array_keys($match->vars);

            array_unshift($keys, 'request');
            array_unshift($vals, $request);

            $match->vars = array_combine($keys, $vals);
        };

        $insert = \Closure::bind($fn, null, $match);
        $insert($match);

        return $match;
    }
}
