<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Exception;

use Exception;
use Lucid\Mux\Matcher\ContextInterface as Match;
use Lucid\Mux\Request\ContextInterface as Request;

/**
 * Class MatchException
 * @package Lucid\Mux\Exception
 * @author  Thomas Appel <mail@thomas-appel.com>
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
        $args[0] = $this->findMismatchReason($args[0]);
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
    public static function noRouteMatch(Request $request, Match $context) : self
    {
        return new self(
            $context,
            sprintf('No route found for requested resource "%s".', $request->getPath())
        );
    }

    /**
     * @param $msg
     *
     * @return string
     */
    private function findMismatchReason($msg) : string
    {
        if ($this->context->isHostMismatch()) {
            return $msg . ' Host mismatch.';
        }

        if ($this->context->isMethodMismatch()) {
            return $msg . ' Method mismatch.';
        }

        if ($this->context->isSchemeMisMatch()) {
            return $msg . ' Protocol mismatch.';
        }

        return $msg;
    }
}
