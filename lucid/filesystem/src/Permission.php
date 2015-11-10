<?php

/*
 * This File is part of the Lucid\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem;

use Lucid\Filesystem\Driver\SupportsPermission;
use Lucid\Filesystem\Driver\SupportsVisibility;

/**
 * @class Permission
 *
 * @package Lucid\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Permission implements PermissionInterface
{
    private $mode;
    private $visibility;

    /**
     * Constructor
     *
     * @param int $mod
     * @param string $visibility
     */
    public function __construct($mode = null, $visibility = null)
    {
        $this->mode = $mode;
        $this->setVisibility($visibility);
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibility($visibility)
    {
        if (null === $visibility) {
            return;
        }

        if (null !== $visibility && null !== $this->mode && self::getVisibilityFromMode($this->mode) !== $visibility ||
            (self::V_PUBLIC !== $visibility && self::V_PRIVATE !== $visibility)
        ) {
            throw new \InvalidArgumentException('Visibility mismatch');
        }

        $this->visibility = $visibility;
    }

    /**
     * {@inheritdoc}
     */
    public function setMode($mod)
    {
        $this->mode = $mod;
        $this->visibility = null;
        $this->getVisibility();
    }

    /**
     * {@inheritdoc}
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibility()
    {
        if (null === $this->visibility && null !== $this->mode) {
            $this->visibility = static::getVisibilityFromMode($this->mode);
        }

        return $this->visibility;
    }

    /**
     * {@inheritdoc}
     */
    public function modeAsString()
    {
        if (null !== $this->mode) {
            return static::filePermsAsString($this->mode);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublic()
    {
        return self::V_PUBLIC === $this->visibility;
    }

    /**
     * supportsVisibility
     *
     * @return boolean
     */
    public function supportsVisibility()
    {
        return null !== $this->visibility || null !== $this->mode;
    }

    /**
     * supportsPermission
     *
     * @return boolean
     */
    public function supportsPermission()
    {
        return null !== $this->permission;
    }

    /**
     * filePermsAsString
     *
     * @param mixed $perm
     *
     * @return string
     */
    public static function filePermsAsString($perm)
    {
        return '0'.decoct(octdec(substr(sprintf('%o', $perm), -4)));
    }

    /**
     * getVisibilityFromMod
     *
     * @param mixed $mod
     *
     * @return string
     */
    public static function getVisibilityFromMode($mode)
    {
        return 0 === (static::sumMode($mode) & 0442) ? self::V_PUBLIC : self::V_PRIVATE;
    }

    /**
     * sumPemMod
     *
     * @param mixed $mod
     *
     * @return array
     */
    protected static function sumMode($mode)
    {
        $modstr = substr(is_int($mode) ? decoct($mode) : (string)$mode, -3);

        return array_sum(str_split($modstr));
    }
}
