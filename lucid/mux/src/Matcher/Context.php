<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Matcher;

use Lucid\Mux\Request\ContextInterface as RequestContext;

/**
 * @class Context
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Context implements ContextInterface
{
    /** @var int */
    private $type;

    /** @var string */
    private $name;

    /** @var RequestContext */
    private $request;

    /** @var mixed */
    private $handler;

    /** @var array */
    private $vars;

    /**
     * Context constructor.
     * @param int $type
     * @param string $name
     * @param RequestContext $request
     * @param $handler
     * @param array $vars
     */
    public function __construct(
        int $type,
        ?string $name,
        RequestContext $request,
        $handler,
        array $vars = []
    ) {
        $this->type    = $type;
        $this->name    = $name;
        $this->request = $request;
        $this->handler = $handler;
        $this->vars    = $vars;
    }

    /**
     * {@inheritdoc}
     */
    public function isMatch() : bool
    {
        return  $this->type === RequestMatcherInterface::MATCH;
    }

    /**
     * {@inheritdoc}
     */
    public function isHostMismatch() : bool
    {
        return $this->type === RequestMatcherInterface::NOMATCH_HOST;
    }

    /**
     * {@inheritdoc}
     */
    public function isMethodMisMatch() : bool
    {
        return $this->type === RequestMatcherInterface::NOMATCH_METHOD;
    }

    /**
     * {@inheritdoc}
     */
    public function isSchemeMisMatch() : bool
    {
        return $this->type === RequestMatcherInterface::NOMATCH_SCHEME;
    }

    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest() : RequestContext
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath() : string
    {
        return $this->request->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * {@inheritdoc}
     */
    public function getVars() : array
    {
        return $this->vars;
    }
}
