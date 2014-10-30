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

/**
 * @class StrictParameterMapper
 *
 * @package Lucid\Module\Routing\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class StrictParameterMapper implements ParameterMapperInterface
{
    /**
     * types
     *
     * @var TypeMapCollectionInterface
     */
    private $types;

    /**
     * Constructor.
     *
     * @param TypeMapCollectionInterface $types
     */
    public function __construct(TypeMapCollectionInterface $types = null)
    {
        $this->types = $types ?: new TypeMapCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function map(HandlerReflector $handler, array $parameters)
    {
        return $this->getParameters($handler, $parameters);
    }

    /**
     * getParameters
     *
     * @param callable $handler
     * @param array $parameters
     *
     * @return array
     */
    protected function getParameters(HandlerReflector $handler, array $parameters)
    {
        $params = [];
        $handlerParams = $handler->getReflector()->getParameters();

        foreach ($handlerParams as $param) {

            if (null !== ($class = $param->getClass())) {
                if (!$this->types->has($class = $class->getName())) {
                    throw new \InvalidArgumentException(
                        sprintf('Cannot map class "%s" to parameter "{$%s}".', $class, $param->getName())
                    );
                }

                $params[$param->getName()] = $this->types->get($class);

            } elseif (!array_key_exists($param->getName(), $parameters)) {
                if (!$param->isOptional()) {
                    throw new \InvalidArgumentException;
                }

                $params[$param->getName()]  = null;

            } else {
                $params[$param->getName()]  = $parameters[$param->getName()];
            }
        }

        return array_values($params);
    }
}
