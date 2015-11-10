<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Object;

/**
 * @interface MemberInterface
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface MemberInterface
{
    const IS_PUBLIC    = 'public';
    const IS_PROTECTED = 'protected';
    const IS_PRIVATE   = 'private';

    const T_VOID       = 'void';
    const T_STRING     = 'string';
    const T_BOOL       = 'boolean';
    const T_INT        = 'integer';
    const T_FLOAT      = 'float';
    const T_ARRAY      = 'array';
    const T_MIXED      = 'mixed';
}
