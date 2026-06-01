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

namespace PhpOffice\PhpPresentation\Shape\Chart;

use PhpOffice\PhpPresentation\ComparableInterface;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Font;
use PhpOffice\PhpPresentation\Style\Outline;

class Axis implements ComparableInterface
{
    public const AXIS_X = 'x';
    public const AXIS_Y = 'y';

    public const TICK_MARK_NONE = 'none';
    public const TICK_MARK_CROSS = 'cross';
    public const TICK_MARK_INSIDE = 'in';
    public const TICK_MARK_OUTSIDE = 'out';

    public const TICK_LABEL_POSITION_NEXT_TO = 'nextTo';
    public const TICK_LABEL_POSITION_HIGH = 'high';
    public const TICK_LABEL_POSITION_LOW = 'low';

    public const CROSSES_AUTO = 'autoZero';
    public const CROSSES_MIN = 'min';
    public const CROSSES_MAX = 'max';

    public const DEFAULT_FORMAT_CODE = 'general';

    /**
     * Title.
     *
     * @var string
     */
    private $title = 'Axis Title';

    /**
     * @var int
     */
    private $titleRotation = 0;

    /**
     * Format code.
     *
     * @var string
     */
    private $formatCode = self::DEFAULT_FORMAT_CODE;

    /**
     * Font.
     *
     * @var Font
     */
    private $font;

    /**
     * Tick lable font.
     *
     * @var Font
     */
    protected $tickLabelFont;

    /**
     * @var null|Gridlines
     */
    protected $majorGridlines;

    /**
     * @var null|Gridlines
     */
    protected $minorGridlines;

    /**
     * @var null|float|int
     */
    protected $minBounds;

    /**
     * @var null|float|int
     */
    protected $maxBounds;

    /**
     * @var string
     */
    protected $crossesAt = self::CROSSES_AUTO;

    /**
     * @var bool
     */
    protected $isReversedOrder = false;

    /**
     * @var string
     */
    protected $minorTickMark = self::TICK_MARK_NONE;

    /**
     * @var string
     */
    protected $majorTickMark = self::TICK_MARK_NONE;

    /**
     * @var string
     */
    protected $tickLabelPosition = self::TICK_LABEL_POSITION_NEXT_TO;

    /**
     * @var float
     */
    protected $minorUnit;

    /**
     * @var float
     */
    protected $majorUnit;

    /**
     * @var Outline
     */
    protected $outline;

    /**
     * @var bool
     */
    protected $isVisible = true;

    /**
     * Create a new \PhpOffice\PhpPresentation\Shape\Chart\Axis instance.
     *
     * @param string $title Title
     */
    public function __construct(string $title = 'Axis Title')
    {
        $this->title = $title;
        // Default the axis line to a visible solid black 1px line. Without an
        // explicit fill PowerPoint receives `<a:ln><a:noFill/></a:ln>` and
        // hides the axis — invisible by default surprised callers, especially
        // for chart types where the axis isn't drawn for free (e.g. AdvancedScatter
        // where both X and Y are c:valAx). Customize via getOutline().
        $this->outline = new Outline();
        $this->outline->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color(Color::COLOR_BLACK));
        $this->font = new Font();
        $this->tickLabelFont = new Font();
    }

    /**
     * Get Title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set Title.
     */
    public function setTitle(string $value = 'Axis Title'): self
    {
        $this->title = $value;

        return $this;
    }

    /**
     * Get font.
     */
    public function getFont(): ?Font
    {
        return $this->font;
    }

    /**
     * Set tick label font.
     */
    public function setTickLabelFont(?Font $font = null): self
    {
        $this->tickLabelFont = $font;

        return $this;
    }

    /**
     * Get tick label font.
     */
    public function getTickLabelFont(): ?Font
    {
        return $this->tickLabelFont;
    }

    /**
     * Set font.
     */
    public function setFont(?Font $font = null): self
    {
        $this->font = $font;

        return $this;
    }

    /**
     * Get Format Code.
     */
    public function getFormatCode(): string
    {
        return $this->formatCode;
    }

    /**
     * Set Format Code.
     */
    public function setFormatCode(string $value = self::DEFAULT_FORMAT_CODE): self
    {
        $this->formatCode = $value;

        return $this;
    }

    /**
     * @return null|float|int
     */
    public function getMinBounds()
    {
        return $this->minBounds;
    }

    /**
     * Set the minimum bound. Accepts int, float, or null.
     *
     * The value is stored as-is so callers that previously passed an integer
     * continue to get an integer back from {@see getMinBounds()}.
     *
     * @param null|float|int $minBounds
     */
    public function setMinBounds($minBounds = null): self
    {
        if (null !== $minBounds && !is_int($minBounds) && !is_float($minBounds)) {
            throw new \TypeError(sprintf(
                'Axis::setMinBounds() expects int, float, or null, %s given',
                gettype($minBounds)
            ));
        }
        $this->minBounds = $minBounds;

        return $this;
    }

    /**
     * @return null|float|int
     */
    public function getMaxBounds()
    {
        return $this->maxBounds;
    }

    /**
     * Set the maximum bound. Accepts int, float, or null.
     *
     * The value is stored as-is so callers that previously passed an integer
     * continue to get an integer back from {@see getMaxBounds()}.
     *
     * @param null|float|int $maxBounds
     */
    public function setMaxBounds($maxBounds = null): self
    {
        if (null !== $maxBounds && !is_int($maxBounds) && !is_float($maxBounds)) {
            throw new \TypeError(sprintf(
                'Axis::setMaxBounds() expects int, float, or null, %s given',
                gettype($maxBounds)
            ));
        }
        $this->maxBounds = $maxBounds;

        return $this;
    }

    public function getCrossesAt(): string
    {
        return $this->crossesAt;
    }

    public function setCrossesAt(string $value = self::CROSSES_AUTO): self
    {
        $this->crossesAt = $value;

        return $this;
    }

    public function isReversedOrder(): bool
    {
        return $this->isReversedOrder;
    }

    public function setIsReversedOrder(bool $value = false): self
    {
        $this->isReversedOrder = $value;

        return $this;
    }

    public function getMajorGridlines(): ?Gridlines
    {
        return $this->majorGridlines;
    }

    public function setMajorGridlines(Gridlines $majorGridlines): self
    {
        $this->majorGridlines = $majorGridlines;

        return $this;
    }

    public function getMinorGridlines(): ?Gridlines
    {
        return $this->minorGridlines;
    }

    public function setMinorGridlines(Gridlines $minorGridlines): self
    {
        $this->minorGridlines = $minorGridlines;

        return $this;
    }

    public function getMinorTickMark(): string
    {
        return $this->minorTickMark;
    }

    public function setMinorTickMark(string $tickMark = self::TICK_MARK_NONE): self
    {
        $this->minorTickMark = $tickMark;

        return $this;
    }

    public function getMajorTickMark(): string
    {
        return $this->majorTickMark;
    }

    public function setMajorTickMark(string $tickMark = self::TICK_MARK_NONE): self
    {
        $this->majorTickMark = $tickMark;

        return $this;
    }

    public function getMinorUnit(): ?float
    {
        return $this->minorUnit;
    }

    /**
     * @param null|float $unit
     */
    public function setMinorUnit($unit = null): self
    {
        $this->minorUnit = $unit;

        return $this;
    }

    public function getMajorUnit(): ?float
    {
        return $this->majorUnit;
    }

    public function setMajorUnit(?float $unit = null): self
    {
        $this->majorUnit = $unit;

        return $this;
    }

    public function getOutline(): Outline
    {
        return $this->outline;
    }

    public function setOutline(Outline $outline): self
    {
        $this->outline = $outline;

        return $this;
    }

    public function getTitleRotation(): int
    {
        return $this->titleRotation;
    }

    public function setTitleRotation(int $titleRotation): self
    {
        if ($titleRotation < 0) {
            $titleRotation = 0;
        }
        if ($titleRotation > 360) {
            $titleRotation = 360;
        }
        $this->titleRotation = $titleRotation;

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        return md5($this->title . $this->formatCode . __CLASS__);
    }

    /**
     * Hash index.
     *
     * @var int
     */
    private $hashIndex;

    /**
     * Get hash index.
     *
     * Note that this index may vary during script execution! Only reliable moment is
     * while doing a write of a workbook and when changes are not allowed.
     *
     * @return null|int Hash index
     */
    public function getHashIndex(): ?int
    {
        return $this->hashIndex;
    }

    /**
     * Set hash index.
     *
     * Note that this index may vary during script execution! Only reliable moment is
     * while doing a write of a workbook and when changes are not allowed.
     *
     * @param int $value Hash index
     *
     * @return self
     */
    public function setHashIndex(int $value)
    {
        $this->hashIndex = $value;

        return $this;
    }

    /**
     * Axis is hidden ?
     */
    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    /**
     * Hide an axis.
     *
     * @param bool $value delete
     */
    public function setIsVisible(bool $value): self
    {
        $this->isVisible = $value;

        return $this;
    }

    public function getTickLabelPosition(): string
    {
        return $this->tickLabelPosition;
    }

    public function setTickLabelPosition(string $value = self::TICK_LABEL_POSITION_NEXT_TO): self
    {
        if (in_array($value, [
            self::TICK_LABEL_POSITION_HIGH,
            self::TICK_LABEL_POSITION_LOW,
            self::TICK_LABEL_POSITION_NEXT_TO,
        ])) {
            $this->tickLabelPosition = $value;
        }

        return $this;
    }
}
