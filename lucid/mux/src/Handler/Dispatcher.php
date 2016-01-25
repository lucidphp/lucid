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

use Lucid\Mux\Matcher\ContextInterface as Match;

/**
 * @class Dispatcher
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Dispatcher implements DispatcherInterface
{
    private $resolver;

    private $mapper;

    /**
     * Constructor.
     *
     * @param ParserInterface $parser
     * @param ParameterMapperInterface $mapper
     */
    public function __construct(ResolverInterface $resolver = null, ParameterMapperInterface $mapper = null)
    {
        $this->resolver = $resolver ?: new Resolver;
        $this->mapper   = $mapper ?: new PassParameterMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(Match $context)
    {
        $args = $this->mapper->map(
            $handler = $this->resolver->resolve($context->getHandler()),
            $context->getVars()
        );

        return $handler->invokeArgs($args);
    }
}
