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
use PhpOffice\PhpPresentation\Style\Fill;

/**
 * Series for AdvancedScatter charts.
 *
 * Each point is a {@see DataPoint} carrying its (X, Y) coordinates plus
 * optional per-point overrides (label text, marker fill, label font, label
 * position). Compared to the legacy key/value {@see Series} model this allows:
 *
 *   - Numeric X values (not just string labels);
 *   - Multiple data points sharing the same X value;
 *   - Per-data-point coloring and per-data-point labels;
 *   - True (X, Y) scatter semantics matching PowerPoint's scatterChart.
 */
class AdvancedScatterSeries extends Series
{
    /**
     * @var array<int, DataPoint>
     */
    private $dataPoints = [];

    /**
     * @param array<int, DataPoint|array{0: float|int|string, 1: float|int|string, 2?: null|string}|array{x: float|int|string, y: float|int|string, title?: null|string}> $dataPoints
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
     *
     * Accepts either explicit coordinates plus an optional label, or a
     * pre-built {@see DataPoint} instance (in which case `$y` and `$title`
     * are ignored).
     *
     * @param DataPoint|float $x
     */
    public function addDataPoint($x, ?float $y = null, ?string $title = null): self
    {
        if ($x instanceof DataPoint) {
            $this->dataPoints[] = $x;

            return $this;
        }
        $this->dataPoints[] = new DataPoint((float) $x, (float) ($y ?? 0.0), $title);

        return $this;
    }

    /**
     * Add a pre-built {@see DataPoint}.
     */
    public function addPoint(DataPoint $point): self
    {
        $this->dataPoints[] = $point;

        return $this;
    }

    /**
     * Replace the series data with the supplied set of points.
     *
     * Accepts a mix of:
     *   - {@see DataPoint} instances
     *   - Positional pairs `[1.0, 2.5]` or triples `[1.0, 2.5, 'Apple']`
     *   - Associative `['x' => 1.0, 'y' => 2.5, 'title' => 'Apple']`
     *
     * @param array<int, DataPoint|array{0: float|int|string, 1: float|int|string, 2?: null|string}|array{x: float|int|string, y: float|int|string, title?: null|string}> $dataPoints
     */
    public function setDataPoints(array $dataPoints): self
    {
        $this->dataPoints = [];
        foreach ($dataPoints as $point) {
            if ($point instanceof DataPoint) {
                $this->dataPoints[] = $point;
                continue;
            }
            if (array_key_exists('x', $point) && array_key_exists('y', $point)) {
                $this->dataPoints[] = new DataPoint(
                    (float) $point['x'],
                    (float) $point['y'],
                    array_key_exists('title', $point) && null !== $point['title']
                        ? (string) $point['title']
                        : null
                );
                continue;
            }
            $this->dataPoints[] = new DataPoint(
                (float) $point[0],
                (float) $point[1],
                array_key_exists(2, $point) && null !== $point[2] ? (string) $point[2] : null
            );
        }

        return $this;
    }

    /**
     * @return array<int, DataPoint>
     */
    public function getDataPoints(): array
    {
        return $this->dataPoints;
    }

    /**
     * Get the X values as a numeric-indexed array.
     *
     * @return array<int, float>
     */
    public function getXValues(): array
    {
        return array_map(static function (DataPoint $point): float {
            return $point->getX();
        }, $this->dataPoints);
    }

    /**
     * Replace the X values, padding/trimming the data point list to match.
     *
     * @param array<int, float|int|string> $values
     */
    public function setXValues(array $values): self
    {
        $values = array_values(array_map('floatval', $values));
        $this->resizeTo(count($values));
        foreach ($values as $i => $x) {
            $this->dataPoints[$i]->setX($x);
        }

        return $this;
    }

    /**
     * Get the Y values as a numeric-indexed array.
     *
     * @return array<int, float>
     */
    public function getYValues(): array
    {
        return array_map(static function (DataPoint $point): float {
            return $point->getY();
        }, $this->dataPoints);
    }

    /**
     * Replace the Y values, padding/trimming the data point list to match.
     *
     * @param array<int, float|int|string> $values
     */
    public function setYValues(array $values): self
    {
        $values = array_values(array_map('floatval', $values));
        $this->resizeTo(count($values));
        foreach ($values as $i => $y) {
            $this->dataPoints[$i]->setY($y);
        }

        return $this;
    }

    public function getPointCount(): int
    {
        return count($this->dataPoints);
    }

    /**
     * Set the visible label for the data point at the supplied index.
     */
    public function setDataPointLabel(int $index, ?string $title): self
    {
        $this->ensurePoint($index);
        $this->dataPoints[$index]->setTitle($title);

        return $this;
    }

    public function getDataPointLabel(int $index): ?string
    {
        return isset($this->dataPoints[$index]) ? $this->dataPoints[$index]->getTitle() : null;
    }

    /**
     * @return array<int, string> Map of data point index → label text (only points that have a label).
     */
    public function getDataPointLabels(): array
    {
        $labels = [];
        foreach ($this->dataPoints as $idx => $point) {
            if ($point->hasTitle()) {
                $labels[$idx] = (string) $point->getTitle();
            }
        }

        return $labels;
    }

    /**
     * Get (or lazily create) the marker fill for the data point at $dataPointIndex.
     *
     * Overridden so that fills set via the legacy `Series` API are routed
     * through the canonical {@see DataPoint} storage.
     */
    public function getDataPointFill(int $dataPointIndex): Fill
    {
        $this->ensurePoint($dataPointIndex);
        $point = $this->dataPoints[$dataPointIndex];
        if (!$point->hasFill()) {
            $point->setFill(new Fill());
        }

        return $point->getFill();
    }

    /**
     * @return array<int, Fill> Map of data point index → fill (only points that have one set).
     */
    public function getDataPointFills(): array
    {
        $fills = [];
        foreach ($this->dataPoints as $idx => $point) {
            if ($point->hasFill()) {
                $fills[$idx] = $point->getFill();
            }
        }

        return $fills;
    }

    /**
     * @return array<int, bool> Map of data point index → marker visibility (only points with an explicit override).
     */
    public function getDataPointMarkerVisibilities(): array
    {
        $visibilities = [];
        foreach ($this->dataPoints as $idx => $point) {
            if ($point->hasMarkerVisibility()) {
                $visibilities[$idx] = (bool) $point->isMarkerVisible();
            }
        }

        return $visibilities;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        $hash = '';
        foreach ($this->dataPoints as $point) {
            $hash .= $point->getHashCode();
        }

        return md5(parent::getHashCode() . $hash . __CLASS__);
    }

    public function __clone()
    {
        parent::__clone();
        $clones = [];
        foreach ($this->dataPoints as $point) {
            $clones[] = clone $point;
        }
        $this->dataPoints = $clones;
    }

    /**
     * Grow / shrink the data point list to the requested length, creating
     * empty (0, 0) points as needed. Used by the parallel-array setters.
     */
    private function resizeTo(int $length): void
    {
        $current = count($this->dataPoints);
        if ($length === $current) {
            return;
        }
        if ($length > $current) {
            for ($i = $current; $i < $length; ++$i) {
                $this->dataPoints[] = new DataPoint();
            }

            return;
        }
        $this->dataPoints = array_slice($this->dataPoints, 0, $length);
    }

    private function ensurePoint(int $index): void
    {
        if ($index < 0) {
            return;
        }
        if (!isset($this->dataPoints[$index])) {
            $needed = $index + 1 - count($this->dataPoints);
            for ($i = 0; $i < $needed; ++$i) {
                $this->dataPoints[] = new DataPoint();
            }
        }
    }
}
