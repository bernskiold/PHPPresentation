<?php

/**
 * This file is part of PHPPresentation - A pure PHP library for reading and writing
 * presentations documents.
 *
 * PHPPresentation is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPPresentation/contributors.
 *
 * @see        https://github.com/PHPOffice/PHPPresentation
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

declare(strict_types=1);

namespace PhpOffice\PhpPresentation\Style;

/**
 * \PhpOffice\PhpPresentation\Style\Outline.
 */
class Outline
{
    public const DASH_SOLID = 'solid';
    public const DASH_DOT = 'dot';
    public const DASH_DASH = 'dash';
    public const DASH_LARGEDASH = 'lgDash';
    public const DASH_DASHDOT = 'dashDot';
    public const DASH_LARGEDASHDOT = 'lgDashDot';
    public const DASH_LARGEDASHDOTDOT = 'lgDashDotDot';
    public const DASH_SYSTEMDASH = 'sysDash';
    public const DASH_SYSTEMDOT = 'sysDot';
    public const DASH_SYSTEMDASHDOT = 'sysDashDot';
    public const DASH_SYSTEMDASHDOTDOT = 'sysDashDotDot';

    /**
     * @var Fill
     */
    protected $fill;

    /**
     * @var int
     */
    protected $width = 1;

    /**
     * Preset dash style (one of the `DASH_*` constants). `null` leaves the
     * line solid, matching PowerPoint's default.
     *
     * @var null|string
     */
    protected $dashStyle;

    public function __construct()
    {
        $this->fill = new Fill();
    }

    public function getFill(): Fill
    {
        return $this->fill;
    }

    public function setFill(Fill $fill): self
    {
        $this->fill = $fill;

        return $this;
    }

    /**
     * Value in pixels.
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Value in pixels.
     */
    public function setWidth(int $pValue = 1): self
    {
        $this->width = $pValue;

        return $this;
    }

    public function getDashStyle(): ?string
    {
        return $this->dashStyle;
    }

    public function hasDashStyle(): bool
    {
        return null !== $this->dashStyle;
    }

    /**
     * @param null|string $pValue One of the `DASH_*` constants, or null for a solid line.
     */
    public function setDashStyle(?string $pValue = null): self
    {
        $this->dashStyle = $pValue;

        return $this;
    }
}
