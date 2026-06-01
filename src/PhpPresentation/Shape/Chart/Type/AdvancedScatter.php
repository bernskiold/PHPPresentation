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

namespace PhpOffice\PhpPresentation\Shape\Chart\Type;

use PhpOffice\PhpPresentation\ComparableInterface;

/**
 * Advanced scatter (XY) chart with proper numeric X and Y axes.
 *
 * Use together with {@see \PhpOffice\PhpPresentation\Shape\Chart\Series\AdvancedScatterSeries}
 * to plot true (X, Y) data — including multiple series, duplicate X values across
 * data points and per-data-point coloring — matching PowerPoint's scatterChart.
 */
class AdvancedScatter extends AbstractTypeLine implements ComparableInterface
{
    public const STYLE_NONE = 'none';
    public const STYLE_LINE = 'line';
    public const STYLE_LINE_MARKER = 'lineMarker';
    public const STYLE_MARKER = 'marker';
    public const STYLE_SMOOTH = 'smooth';
    public const STYLE_SMOOTH_MARKER = 'smoothMarker';

    /**
     * @var array<int, string>
     */
    public static $arrayStyle = [
        self::STYLE_NONE,
        self::STYLE_LINE,
        self::STYLE_LINE_MARKER,
        self::STYLE_MARKER,
        self::STYLE_SMOOTH,
        self::STYLE_SMOOTH_MARKER,
    ];

    /**
     * @var string
     */
    protected $scatterStyle = self::STYLE_MARKER;

    /**
     * Whether PowerPoint should automatically vary the colors of data points
     * within a single series. Useful when you want each point in a single-series
     * scatter to receive a different color without specifying per-point fills.
     *
     * @var bool
     */
    protected $varyColors = false;

    public function getScatterStyle(): string
    {
        return $this->scatterStyle;
    }

    public function setScatterStyle(string $value = self::STYLE_MARKER): self
    {
        if (in_array($value, self::$arrayStyle, true)) {
            $this->scatterStyle = $value;
        }

        return $this;
    }

    public function hasVaryColors(): bool
    {
        return $this->varyColors;
    }

    public function setVaryColors(bool $value = false): self
    {
        $this->varyColors = $value;

        return $this;
    }

    /**
     * For AdvancedScatter both X and Y are numeric value axes (c:valAx).
     */
    public function isXAxisValueAxis(): bool
    {
        return true;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        $hash = '';
        foreach ($this->getSeries() as $series) {
            $hash .= $series->getHashCode();
        }

        return md5(parent::getHashCode() . $this->scatterStyle . ($this->varyColors ? '1' : '0') . $hash . __CLASS__);
    }
}
