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

use UnexpectedValueException;
use Lucid\Mux\Exception\MissingValueException;

/**
 * @class StrictParameterMapper
 *
 * @package Lucid\Mux
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
     *
     * @throws UnexpectedValueException if required type cannot be mapped.
     * @throws UnexpectedValueException if a none optional parameter is
     * missing
     *
     * @return array
     */
    public function map(Reflector $handler, array $parameters)
    {
        $params = [];

        foreach ($handler->getReflector()->getParameters() as $param) {
            if (null !== ($class = $param->getClass())) {
                if (!$this->types->has($class = $class->getName())) {
                    throw new MissingValueException(
                        sprintf('Cannot map class "%s" to parameter "{$%s}".', $class, $param->getName())
                    );
                }

                $params[$param->getName()] = $this->types->get($class);
                continue;
            }

            if (array_key_exists($param->getName(), $parameters)) {
                $params[$param->getName()]  = $parameters[$param->getName()];
                continue;
            }

            if (!$param->isOptional()) {
                throw new MissingValueException(
                    sprintf('Missing non optional parameter "{$%s}".', $param->getName())
                );
            }

            $params[$param->getName()]  = null;
        }

        return array_values($params);
    }
}
