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

namespace PhpOffice\PhpPresentation\Tests\Shape\Chart\Series;

use PhpOffice\PhpPresentation\Shape\Chart\Series;
use PhpOffice\PhpPresentation\Shape\Chart\Series\DataPoint;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Font;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PhpOffice\PhpPresentation\Shape\Chart\Series\DataPoint
 */
class DataPointTest extends TestCase
{
    public function testDefaults(): void
    {
        $point = new DataPoint();

        self::assertSame(0.0, $point->getX());
        self::assertSame(0.0, $point->getY());
        self::assertNull($point->getTitle());
        self::assertFalse($point->hasTitle());
        self::assertNull($point->getFill());
        self::assertFalse($point->hasFill());
        self::assertNull($point->getFont());
        self::assertFalse($point->hasFont());
        self::assertNull($point->getLabelPosition());
        self::assertFalse($point->hasLabelPosition());
    }

    public function testConstructor(): void
    {
        $point = new DataPoint(1.5, 2.5, 'Apple');

        self::assertSame(1.5, $point->getX());
        self::assertSame(2.5, $point->getY());
        self::assertSame('Apple', $point->getTitle());
        self::assertTrue($point->hasTitle());
    }

    public function testEmptyTitleIsNotConsideredPresent(): void
    {
        $point = new DataPoint(0.0, 0.0, '');
        self::assertFalse($point->hasTitle());
    }

    public function testFluentSetters(): void
    {
        $point = new DataPoint();

        self::assertInstanceOf(DataPoint::class, $point->setX(10.0));
        self::assertInstanceOf(DataPoint::class, $point->setY(20.0));
        self::assertInstanceOf(DataPoint::class, $point->setTitle('Hi'));
        self::assertInstanceOf(DataPoint::class, $point->setLabelPosition(Series::LABEL_TOP));

        self::assertSame(10.0, $point->getX());
        self::assertSame(20.0, $point->getY());
        self::assertSame('Hi', $point->getTitle());
        self::assertSame(Series::LABEL_TOP, $point->getLabelPosition());
    }

    public function testFillRoundTrip(): void
    {
        $fill = (new Fill())->setFillType(Fill::FILL_SOLID)->setStartColor(new Color(Color::COLOR_RED));
        $point = (new DataPoint())->setFill($fill);

        self::assertTrue($point->hasFill());
        self::assertSame($fill, $point->getFill());
    }

    public function testFontRoundTrip(): void
    {
        $font = (new Font())->setSize(14)->setBold(true);
        $point = (new DataPoint())->setFont($font);

        self::assertTrue($point->hasFont());
        self::assertSame($font, $point->getFont());
    }

    public function testHashCodeChangesWithEachProperty(): void
    {
        $base = new DataPoint(1.0, 2.0);
        $hash = $base->getHashCode();

        self::assertNotSame($hash, (new DataPoint(1.5, 2.0))->getHashCode());
        self::assertNotSame($hash, (new DataPoint(1.0, 2.5))->getHashCode());
        self::assertNotSame($hash, (new DataPoint(1.0, 2.0, 'A'))->getHashCode());

        $withPosition = (new DataPoint(1.0, 2.0))->setLabelPosition(Series::LABEL_TOP);
        self::assertNotSame($hash, $withPosition->getHashCode());
    }

    public function testCloneDeepCopiesFillAndFont(): void
    {
        $point = (new DataPoint(1.0, 2.0))
            ->setFill(new Fill())
            ->setFont(new Font());

        $clone = clone $point;
        $clone->getFont()->setSize(99);

        self::assertNotSame($point->getFill(), $clone->getFill());
        self::assertNotSame($point->getFont(), $clone->getFont());
        self::assertNotSame(99, $point->getFont()->getSize());
    }
}
