<?php

/*
 * This File is part of the Lucid\Module\Routing\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Handler;

use Lucid\Module\Routing\Matcher\MatchContextInterface;

/**
 * @class HandlerDispatcher
 *
 * @package Lucid\Module\Routing\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class HandlerDispatcher implements HandlerDispatcherInterface
{
    private $parser;
    private $mapper;

    /**
     * Constructor.
     *
     * @param HandlerParserInterface $parser
     * @param ParameterMapperInterface $mapper
     *
     * @return void
     */
    public function __construct(
        HandlerParserInterface $parser = null,
        ParameterMapperInterface $mapper = null
    ) {
        $this->parser = $parser ?: new HandlerParser;
        $this->mapper = $mapper ?: new StrictParameterMapper;
    }

    /**
     * dispatchHandler
     *
     * @param MatchContextInterface $context
     *
     * @return mixed
     */
    public function dispatchHandler(MatchContextInterface $context)
    {
        $args = $this->mapper->map(
            $handler = $this->parser->parse($context->getHandler()),
            $context->getParameters()
        );

        return $handler->invokeArgs($args);
    }
}
