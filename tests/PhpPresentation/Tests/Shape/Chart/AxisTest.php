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

namespace PhpOffice\PhpPresentation\Tests\Shape\Chart;

use PhpOffice\PhpPresentation\Shape\Chart\Axis;
use PhpOffice\PhpPresentation\Shape\Chart\Gridlines;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Font;
use PhpOffice\PhpPresentation\Style\Outline;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Axis element.
 *
 * @coversDefaultClass \PhpOffice\PhpPresentation\Shape\Chart\Axis
 */
class AxisTest extends TestCase
{
    public function testConstruct(): void
    {
        $object = new Axis();

        self::assertEquals('Axis Title', $object->getTitle());
        self::assertInstanceOf(Font::class, $object->getFont());
        self::assertNull($object->getMinorGridlines());
        self::assertNull($object->getMajorGridlines());
    }

    public function testBounds(): void
    {
        $value = mt_rand(0, 100);
        $object = new Axis();

        self::assertNull($object->getMinBounds());
        self::assertInstanceOf(Axis::class, $object->setMinBounds($value));
        self::assertEquals($value, $object->getMinBounds());
        self::assertInstanceOf(Axis::class, $object->setMinBounds());
        self::assertNull($object->getMinBounds());

        self::assertNull($object->getMaxBounds());
        self::assertInstanceOf(Axis::class, $object->setMaxBounds($value));
        self::assertEquals($value, $object->getMaxBounds());
        self::assertInstanceOf(Axis::class, $object->setMaxBounds());
        self::assertNull($object->getMaxBounds());
    }

    public function testFloatBoundsArePreserved(): void
    {
        $object = new Axis();

        $object->setMinBounds(-0.2)->setMaxBounds(0.15);

        self::assertSame(-0.2, $object->getMinBounds());
        self::assertSame(0.15, $object->getMaxBounds());
    }

    public function testIntegerBoundsAreReturnedAsIntegers(): void
    {
        // Backwards-compat: callers that stored an int still see an int back
        // (no implicit widening to float).
        $object = new Axis();

        $object->setMinBounds(5)->setMaxBounds(10);

        self::assertSame(5, $object->getMinBounds());
        self::assertSame(10, $object->getMaxBounds());
    }

    public function testRejectsNonNumericBounds(): void
    {
        $object = new Axis();

        $this->expectException(\TypeError::class);
        // @phpstan-ignore-next-line — intentionally passing a string to verify the runtime guard
        $object->setMinBounds('not-a-number');
    }

    public function testCrossesAt(): void
    {
        $object = new Axis();

        self::assertEquals(Axis::CROSSES_AUTO, $object->getCrossesAt());
        self::assertInstanceOf(Axis::class, $object->setCrossesAt(Axis::CROSSES_MAX));
        self::assertEquals(Axis::CROSSES_MAX, $object->getCrossesAt());
    }

    public function testIsReversedOrder(): void
    {
        $object = new Axis();
        self::assertFalse($object->isReversedOrder());
        self::assertInstanceOf(Axis::class, $object->setIsReversedOrder(true));
        self::assertTrue($object->isReversedOrder());
        self::assertInstanceOf(Axis::class, $object->setIsReversedOrder(false));
        self::assertFalse($object->isReversedOrder());
    }

    public function testFont(): void
    {
        $object = new Axis();

        self::assertInstanceOf(Axis::class, $object->setFont());
        self::assertNull($object->getFont());
        self::assertInstanceOf(Axis::class, $object->setFont(new Font()));
        self::assertInstanceOf(Font::class, $object->getFont());
    }

    public function testFormatCode(): void
    {
        $object = new Axis();
        self::assertEquals(Axis::DEFAULT_FORMAT_CODE, $object->getFormatCode());
        self::assertInstanceOf(Axis::class, $object->setFormatCode());
        self::assertEquals(Axis::DEFAULT_FORMAT_CODE, $object->getFormatCode());
        self::assertInstanceOf(Axis::class, $object->setFormatCode('AAAA'));
        self::assertEquals('AAAA', $object->getFormatCode());
    }

    public function testGridLines(): void
    {
        $object = new Axis();

        /** @var Gridlines $oMock */
        $oMock = $this->getMockBuilder(Gridlines::class)->getMock();

        self::assertInstanceOf(Axis::class, $object->setMajorGridlines($oMock));
        self::assertInstanceOf(Gridlines::class, $object->getMajorGridlines());
        self::assertInstanceOf(Axis::class, $object->setMinorGridlines($oMock));
        self::assertInstanceOf(Gridlines::class, $object->getMinorGridlines());
    }

    public function testHashIndex(): void
    {
        $object = new Axis();
        $value = mt_rand(1, 100);

        self::assertEmpty($object->getHashIndex());
        self::assertInstanceOf(Axis::class, $object->setHashIndex($value));
        self::assertEquals($value, $object->getHashIndex());
    }

    public function testIsVisible(): void
    {
        $object = new Axis();
        self::assertTrue($object->isVisible());
        self::assertInstanceOf(Axis::class, $object->setIsVisible(false));
        self::assertFalse($object->isVisible());
        self::assertInstanceOf(Axis::class, $object->setIsVisible(true));
        self::assertTrue($object->isVisible());
    }

    public function testLabelRotation(): void
    {
        $object = new Axis();
        self::assertEquals(0, $object->getTitleRotation());
        self::assertInstanceOf(Axis::class, $object->setTitleRotation(-1));
        self::assertEquals(0, $object->getTitleRotation());
        self::assertInstanceOf(Axis::class, $object->setTitleRotation(361));
        self::assertEquals(360, $object->getTitleRotation());
        $value = mt_rand(0, 360);
        self::assertInstanceOf(Axis::class, $object->setTitleRotation($value));
        self::assertEquals($value, $object->getTitleRotation());
    }

    public function testOutline(): void
    {
        /** @var Outline $oMock */
        $oMock = $this->getMockBuilder(Outline::class)->getMock();

        $object = new Axis();
        self::assertInstanceOf(Outline::class, $object->getOutline());
        self::assertInstanceOf(Axis::class, $object->setOutline($oMock));
        self::assertInstanceOf(Outline::class, $object->getOutline());
    }

    public function testDefaultOutlineIsVisibleSolidBlack(): void
    {
        $object = new Axis();
        $outline = $object->getOutline();

        self::assertSame(Fill::FILL_SOLID, $outline->getFill()->getFillType());
        self::assertSame(Color::COLOR_BLACK, $outline->getFill()->getStartColor()->getARGB());
        self::assertSame(1, $outline->getWidth());
    }

    public function testOutlineColorAndWidthAreCustomizable(): void
    {
        $object = new Axis();
        $object->getOutline()->setWidth(4);
        $object->getOutline()->getFill()->getStartColor()->setRGB('ABCDEF');

        self::assertSame(4, $object->getOutline()->getWidth());
        self::assertSame('FFABCDEF', $object->getOutline()->getFill()->getStartColor()->getARGB());
    }

    public function testOutlineCanBeHiddenByOptingOutOfFill(): void
    {
        $object = new Axis();
        $object->getOutline()->getFill()->setFillType(Fill::FILL_NONE);

        self::assertSame(Fill::FILL_NONE, $object->getOutline()->getFill()->getFillType());
    }

    public function testTickLabelFont(): void
    {
        $object = new Axis();

        self::assertInstanceOf(Font::class, $object->getTickLabelFont());
        self::assertInstanceOf(Axis::class, $object->setTickLabelFont());
        self::assertNull($object->getTickLabelFont());
        self::assertInstanceOf(Axis::class, $object->setTickLabelFont(new Font()));
        self::assertInstanceOf(Font::class, $object->getTickLabelFont());
    }

    public function testTickLabelPosition(): void
    {
        $object = new Axis();

        self::assertEquals(Axis::TICK_LABEL_POSITION_NEXT_TO, $object->getTickLabelPosition());
        self::assertInstanceOf(Axis::class, $object->setTickLabelPosition(Axis::TICK_LABEL_POSITION_HIGH));
        self::assertEquals(Axis::TICK_LABEL_POSITION_HIGH, $object->getTickLabelPosition());
        self::assertInstanceOf(Axis::class, $object->setTickLabelPosition(Axis::TICK_LABEL_POSITION_NEXT_TO));
        self::assertEquals(Axis::TICK_LABEL_POSITION_NEXT_TO, $object->getTickLabelPosition());
        self::assertInstanceOf(Axis::class, $object->setTickLabelPosition(Axis::TICK_LABEL_POSITION_LOW));
        self::assertEquals(Axis::TICK_LABEL_POSITION_LOW, $object->getTickLabelPosition());
        self::assertInstanceOf(Axis::class, $object->setTickLabelPosition('Unauthorized'));
        self::assertEquals(Axis::TICK_LABEL_POSITION_LOW, $object->getTickLabelPosition());
    }

    public function testTickMark(): void
    {
        $value = Axis::TICK_MARK_INSIDE;
        $object = new Axis();

        self::assertEquals(Axis::TICK_MARK_NONE, $object->getMinorTickMark());
        self::assertInstanceOf(Axis::class, $object->setMinorTickMark($value));
        self::assertEquals($value, $object->getMinorTickMark());
        self::assertInstanceOf(Axis::class, $object->setMinorTickMark());
        self::assertEquals(Axis::TICK_MARK_NONE, $object->getMinorTickMark());

        self::assertEquals(Axis::TICK_MARK_NONE, $object->getMajorTickMark());
        self::assertInstanceOf(Axis::class, $object->setMajorTickMark($value));
        self::assertEquals($value, $object->getMajorTickMark());
        self::assertInstanceOf(Axis::class, $object->setMajorTickMark());
        self::assertEquals(Axis::TICK_MARK_NONE, $object->getMajorTickMark());
    }

    public function testTitle(): void
    {
        $object = new Axis();
        self::assertEquals('Axis Title', $object->getTitle());
        self::assertInstanceOf(Axis::class, $object->setTitle('AAAA'));
        self::assertEquals('AAAA', $object->getTitle());
    }

    public function testUnit(): void
    {
        $value = mt_rand(0, 100);
        $object = new Axis();

        self::assertNull($object->getMinorUnit());
        self::assertInstanceOf(Axis::class, $object->setMinorUnit($value));
        self::assertEquals($value, $object->getMinorUnit());
        self::assertInstanceOf(Axis::class, $object->setMinorUnit());
        self::assertNull($object->getMinorUnit());

        self::assertNull($object->getMajorUnit());
        self::assertInstanceOf(Axis::class, $object->setMajorUnit($value));
        self::assertEquals($value, $object->getMajorUnit());
        self::assertInstanceOf(Axis::class, $object->setMajorUnit());
        self::assertNull($object->getMajorUnit());
    }
}
