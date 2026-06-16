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

use PhpOffice\PhpPresentation\ComparableInterface;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Font;

/**
 * Single (X, Y) data point in an {@see AdvancedScatterSeries}, optionally
 * carrying its own visible label and per-point styling overrides:
 *
 *   - title         : label text shown next to the point (rendered as a custom
 *                     `c:dLbl` in the OOXML scatterChart).
 *   - fill          : marker fill color (overrides the series-level marker fill).
 *   - font          : font for the data label (color, size, bold, italic, etc.).
 *   - labelPosition : where to place the label relative to the marker
 *                     (use {@see \PhpOffice\PhpPresentation\Shape\Chart\Series}
 *                     LABEL_* constants — `t`, `b`, `l`, `r`, `ctr`, …).
 */
class DataPoint implements ComparableInterface
{
    /**
     * @var float
     */
    private $x = 0.0;

    /**
     * @var float
     */
    private $y = 0.0;

    /**
     * @var null|string
     */
    private $title;

    /**
     * @var null|Fill
     */
    private $fill;

    /**
     * @var null|Font
     */
    private $font;

    /**
     * @var null|string
     */
    private $labelPosition;

    /**
     * Per-point marker visibility override.
     *
     * `null` inherits the series-level marker; `false` hides the marker for
     * this point only (emitted as `<c:dPt><c:marker><c:symbol val="none"/>`);
     * `true` forces a circle marker. Used to draw connector "spokes" whose
     * shared anchor point should not render its own marker.
     *
     * @var null|bool
     */
    private $markerVisible;

    /**
     * @var int
     */
    private $hashIndex = 0;

    public function __construct(float $x = 0.0, float $y = 0.0, ?string $title = null)
    {
        $this->x = $x;
        $this->y = $y;
        $this->title = $title;
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function setX(float $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function setY(float $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function hasTitle(): bool
    {
        return null !== $this->title && '' !== $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFill(): ?Fill
    {
        return $this->fill;
    }

    public function hasFill(): bool
    {
        return null !== $this->fill;
    }

    public function setFill(?Fill $fill): self
    {
        $this->fill = $fill;

        return $this;
    }

    public function getFont(): ?Font
    {
        return $this->font;
    }

    public function hasFont(): bool
    {
        return null !== $this->font;
    }

    public function setFont(?Font $font): self
    {
        $this->font = $font;

        return $this;
    }

    public function getLabelPosition(): ?string
    {
        return $this->labelPosition;
    }

    public function hasLabelPosition(): bool
    {
        return null !== $this->labelPosition;
    }

    public function setLabelPosition(?string $position): self
    {
        $this->labelPosition = $position;

        return $this;
    }

    public function isMarkerVisible(): ?bool
    {
        return $this->markerVisible;
    }

    public function hasMarkerVisibility(): bool
    {
        return null !== $this->markerVisible;
    }

    public function setMarkerVisible(?bool $visible): self
    {
        $this->markerVisible = $visible;

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        return md5(
            (string) $this->x
            . '|' . (string) $this->y
            . '|' . (null === $this->title ? 'null' : $this->title)
            . '|' . (null === $this->fill ? 'null' : $this->fill->getHashCode())
            . '|' . (null === $this->font ? 'null' : $this->font->getHashCode())
            . '|' . (null === $this->labelPosition ? 'null' : $this->labelPosition)
            . '|' . (null === $this->markerVisible ? 'null' : ($this->markerVisible ? '1' : '0'))
            . '|' . __CLASS__
        );
    }

    public function getHashIndex(): ?int
    {
        return $this->hashIndex;
    }

    public function setHashIndex(int $value): self
    {
        $this->hashIndex = $value;

        return $this;
    }

    public function __clone()
    {
        if (null !== $this->fill) {
            $this->fill = clone $this->fill;
        }
        if (null !== $this->font) {
            $this->font = clone $this->font;
        }
    }
}
