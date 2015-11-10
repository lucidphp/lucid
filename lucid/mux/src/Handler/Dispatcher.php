<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Handler;

use Lucid\Mux\Matcher\ContextInterface;

/**
 * @class Dispatcher
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Dispatcher implements DispatcherInterface
{

    private $parser;
    private $mapper;

    /**
     * Constructor.
     *
     * @param ParserInterface $parser
     * @param ParameterMapperInterface $mapper
     */
    public function __construct(
        ParserInterface $parser = null,
        ParameterMapperInterface $mapper = null
    ) {
        $this->parser = $parser ?: new Parser;
        $this->mapper = $mapper ?: new PassParameterMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(ContextInterface $context)
    {
        $args = $this->mapper->map(
            $handler = $this->parser->parse($context->getHandler()),
            $context->getParameters()
        );

        return $handler->invokeArgs($args);
    }
}
