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

use PhpOffice\PhpPresentation\Shape\Chart\Series\AdvancedScatterSeries;
use PhpOffice\PhpPresentation\Style\Color;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PhpOffice\PhpPresentation\Shape\Chart\Series\AdvancedScatterSeries
 */
class AdvancedScatterSeriesTest extends TestCase
{
    public function testDefaults(): void
    {
        $series = new AdvancedScatterSeries();

        self::assertSame('Series Title', $series->getTitle());
        self::assertSame([], $series->getXValues());
        self::assertSame([], $series->getYValues());
        self::assertSame([], $series->getDataPoints());
        self::assertSame(0, $series->getPointCount());
    }

    public function testConstructWithPositionalPairs(): void
    {
        $series = new AdvancedScatterSeries('Sales', [
            [1.0, 10.5],
            [2.5, 20.0],
            [2.5, 15.0], // duplicate X allowed
        ]);

        self::assertSame('Sales', $series->getTitle());
        self::assertSame([1.0, 2.5, 2.5], $series->getXValues());
        self::assertSame([10.5, 20.0, 15.0], $series->getYValues());
        self::assertSame(3, $series->getPointCount());
    }

    public function testConstructWithAssociativePairs(): void
    {
        $series = new AdvancedScatterSeries('Sales', [
            ['x' => 1, 'y' => 10],
            ['x' => 3, 'y' => 25.5],
        ]);

        self::assertSame([1.0, 3.0], $series->getXValues());
        self::assertSame([10.0, 25.5], $series->getYValues());
    }

    public function testAddDataPoint(): void
    {
        $series = new AdvancedScatterSeries();

        self::assertInstanceOf(AdvancedScatterSeries::class, $series->addDataPoint(1.0, 2.0));
        $series->addDataPoint(3.5, 4.0);

        self::assertSame([1.0, 3.5], $series->getXValues());
        self::assertSame([2.0, 4.0], $series->getYValues());
        self::assertSame([[1.0, 2.0], [3.5, 4.0]], $series->getDataPoints());
        self::assertSame(2, $series->getPointCount());
    }

    public function testSetDataPointsReplacesExisting(): void
    {
        $series = new AdvancedScatterSeries();
        $series->addDataPoint(99.0, 99.0);

        $series->setDataPoints([[1.0, 2.0], [3.0, 4.0]]);

        self::assertSame([1.0, 3.0], $series->getXValues());
        self::assertSame([2.0, 4.0], $series->getYValues());
    }

    public function testSetXValuesAndYValuesIndependently(): void
    {
        $series = new AdvancedScatterSeries();
        $series->setXValues([1, 2, 3]);
        $series->setYValues([10.0, 20.0, '30']);

        self::assertSame([1.0, 2.0, 3.0], $series->getXValues());
        self::assertSame([10.0, 20.0, 30.0], $series->getYValues());
    }

    public function testPointCountReportsMinOfXandY(): void
    {
        $series = new AdvancedScatterSeries();
        $series->setXValues([1, 2, 3, 4]);
        $series->setYValues([10, 20]);

        self::assertSame(2, $series->getPointCount());
    }

    public function testDataPointFillFromBaseSeriesIsAvailable(): void
    {
        $series = new AdvancedScatterSeries('Test', [[1.0, 2.0], [3.0, 4.0]]);

        $fill = $series->getDataPointFill(1);
        $fill->setStartColor(new Color(Color::COLOR_RED));

        self::assertSame(Color::COLOR_RED, $series->getDataPointFill(1)->getStartColor()->getARGB());
    }

    public function testHashCodeReflectsData(): void
    {
        $a = new AdvancedScatterSeries('S', [[1.0, 2.0]]);
        $b = new AdvancedScatterSeries('S', [[1.0, 2.0]]);
        $c = new AdvancedScatterSeries('S', [[1.0, 3.0]]);

        self::assertSame($a->getHashCode(), $b->getHashCode());
        self::assertNotSame($a->getHashCode(), $c->getHashCode());
    }
}
