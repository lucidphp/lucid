<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Exception;

use Exception;
use Lucid\Mux\Matcher\ContextInterface as Match;
use Lucid\Mux\Request\ContextInterface as Request;

/**
 * @class MatchException
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MatchException extends \RuntimeException
{
    /** @var Match  */
    private $context;

    /**
     * MatchException constructor.
     *
     * @param Match $context
     * @param array ...$args
     * @var Match $context
     * @var string $message
     * @var int $code
     * @var Exception $previous
     */
    public function __construct(Match $context, ...$args)
    {
        $this->context = $context;
        parent::__construct(...$args);
    }

    /**
     * @return Match
     */
    public function getMatchContext() : Match
    {
        return $this->context;
    }

    /**
     * @param Request $request
     * @param Match $context
     *
     * @return MatchException
     */
    public static function noRouteMatch(Request $request, Match $context)
    {
        return new self(
            $context,
            sprintf('No route found for requested resource "%s".', $request->getPath())
        );
    }
}
