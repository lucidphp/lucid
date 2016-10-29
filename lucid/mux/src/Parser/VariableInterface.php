<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: malcolm
 * Date: 15.10.16
 * Time: 11:28
 */

namespace Lucid\Mux\Parser;

interface VariableInterface extends TokenInterface
{
    /**
     * @return bool
     */
    public function isRequired() : bool;

    /**
     * @return string
     */
    public function getRegex() : string;
}
