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

namespace PhpOffice\PhpPresentation\Shape\Chart\Series;

use PhpOffice\PhpPresentation\Shape\Chart\Series;

/**
 * Series for AdvancedScatter charts.
 *
 * Unlike the legacy {@see Series} key/value model where the array key acts as the
 * (categorical) X value, this class stores X and Y coordinates as parallel
 * numeric arrays. This allows:
 *
 *   - Numeric X values (not just string labels);
 *   - Multiple data points sharing the same X value;
 *   - True (X, Y) scatter semantics matching PowerPoint's scatterChart.
 *
 * Per-data-point fill (color) is inherited from {@see Series::getDataPointFill()}.
 */
class AdvancedScatterSeries extends Series
{
    /**
     * @var array<int, float>
     */
    private $xValues = [];

    /**
     * @var array<int, float>
     */
    private $yValues = [];

    /**
     * @param array<int, array{0: float|int|string, 1: float|int|string}|array{x: float|int|string, y: float|int|string}> $dataPoints
     */
    public function __construct(string $title = 'Series Title', array $dataPoints = [])
    {
        parent::__construct($title);
        if (!empty($dataPoints)) {
            $this->setDataPoints($dataPoints);
        }
    }

    /**
     * Add a single (X, Y) data point.
     */
    public function addDataPoint(float $x, float $y): self
    {
        $this->xValues[] = $x;
        $this->yValues[] = $y;

        return $this;
    }

    /**
     * Replace the series data with the supplied set of (X, Y) points.
     *
     * Accepts either positional pairs `[[1.0, 2.5], [2.0, 3.1]]` or associative
     * pairs `[['x' => 1.0, 'y' => 2.5], ...]`.
     *
     * @param array<int, array{0: float|int|string, 1: float|int|string}|array{x: float|int|string, y: float|int|string}> $dataPoints
     */
    public function setDataPoints(array $dataPoints): self
    {
        $this->xValues = [];
        $this->yValues = [];
        foreach ($dataPoints as $point) {
            if (array_key_exists('x', $point) && array_key_exists('y', $point)) {
                $this->addDataPoint((float) $point['x'], (float) $point['y']);
            } else {
                $this->addDataPoint((float) $point[0], (float) $point[1]);
            }
        }

        return $this;
    }

    /**
     * Get the data points as positional pairs.
     *
     * @return array<int, array{0: float, 1: float}>
     */
    public function getDataPoints(): array
    {
        $points = [];
        $count = count($this->xValues);
        for ($i = 0; $i < $count; ++$i) {
            $points[] = [$this->xValues[$i], $this->yValues[$i]];
        }

        return $points;
    }

    /**
     * Get the raw X values as a numeric-indexed array.
     *
     * @return array<int, float>
     */
    public function getXValues(): array
    {
        return $this->xValues;
    }

    /**
     * Replace the X values. Must match the length of the Y values.
     *
     * @param array<int, float|int|string> $values
     */
    public function setXValues(array $values): self
    {
        $this->xValues = array_values(array_map('floatval', $values));

        return $this;
    }

    /**
     * Get the raw Y values as a numeric-indexed array.
     *
     * @return array<int, float>
     */
    public function getYValues(): array
    {
        return $this->yValues;
    }

    /**
     * Replace the Y values. Must match the length of the X values.
     *
     * @param array<int, float|int|string> $values
     */
    public function setYValues(array $values): self
    {
        $this->yValues = array_values(array_map('floatval', $values));

        return $this;
    }

    /**
     * Number of (X, Y) points in the series.
     */
    public function getPointCount(): int
    {
        return min(count($this->xValues), count($this->yValues));
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        return md5(parent::getHashCode() . var_export($this->xValues, true) . var_export($this->yValues, true) . __CLASS__);
    }
}
