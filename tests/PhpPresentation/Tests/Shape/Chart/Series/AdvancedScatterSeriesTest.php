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
use PhpOffice\PhpPresentation\Shape\Chart\Series\AdvancedScatterSeries;
use PhpOffice\PhpPresentation\Shape\Chart\Series\DataPoint;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Font;
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
        self::assertSame([], $series->getDataPointLabels());
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
        self::assertContainsOnlyInstancesOf(DataPoint::class, $series->getDataPoints());
    }

    public function testConstructWithPositionalTriplesIncludesTitles(): void
    {
        $series = new AdvancedScatterSeries('Brands', [
            [1.0, 10.0, 'Apple'],
            [2.0, 20.0, 'Adidas'],
            [3.0, 30.0],
        ]);

        $points = $series->getDataPoints();
        self::assertSame('Apple', $points[0]->getTitle());
        self::assertSame('Adidas', $points[1]->getTitle());
        self::assertNull($points[2]->getTitle());
        self::assertSame(['0' => 'Apple', '1' => 'Adidas'], $series->getDataPointLabels());
    }

    public function testConstructWithAssociativePairs(): void
    {
        $series = new AdvancedScatterSeries('Sales', [
            ['x' => 1, 'y' => 10, 'title' => 'Apple'],
            ['x' => 3, 'y' => 25.5],
        ]);

        self::assertSame([1.0, 3.0], $series->getXValues());
        self::assertSame([10.0, 25.5], $series->getYValues());
        $points = $series->getDataPoints();
        self::assertSame('Apple', $points[0]->getTitle());
        self::assertNull($points[1]->getTitle());
    }

    public function testConstructFromDataPointInstances(): void
    {
        $a = new DataPoint(1.0, 2.0, 'A');
        $b = new DataPoint(3.0, 4.0);
        $series = new AdvancedScatterSeries('Mixed', [$a, $b]);

        self::assertSame([$a, $b], $series->getDataPoints());
    }

    public function testAddDataPoint(): void
    {
        $series = new AdvancedScatterSeries();

        self::assertInstanceOf(AdvancedScatterSeries::class, $series->addDataPoint(1.0, 2.0));
        $series->addDataPoint(3.5, 4.0, 'Tagged');

        self::assertSame([1.0, 3.5], $series->getXValues());
        self::assertSame([2.0, 4.0], $series->getYValues());
        self::assertSame(2, $series->getPointCount());
        $points = $series->getDataPoints();
        self::assertNull($points[0]->getTitle());
        self::assertSame('Tagged', $points[1]->getTitle());
    }

    public function testAddPointAcceptsDataPoint(): void
    {
        $series = new AdvancedScatterSeries();
        $point = new DataPoint(7.0, 8.0, 'P');

        $series->addPoint($point);

        self::assertSame([$point], $series->getDataPoints());
    }

    public function testAddDataPointAlsoAcceptsDataPointAsFirstArg(): void
    {
        $series = new AdvancedScatterSeries();
        $point = new DataPoint(7.0, 8.0);

        $series->addDataPoint($point);

        self::assertSame([$point], $series->getDataPoints());
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

    public function testSetXValuesPreservesExistingTitlesAndPadsToLength(): void
    {
        $series = new AdvancedScatterSeries('s', [[1, 2, 'A'], [3, 4, 'B']]);
        $series->setXValues([10, 20, 30]);

        self::assertSame([10.0, 20.0, 30.0], $series->getXValues());
        self::assertSame([2.0, 4.0, 0.0], $series->getYValues());
        self::assertSame('A', $series->getDataPoints()[0]->getTitle());
        self::assertSame('B', $series->getDataPoints()[1]->getTitle());
    }

    public function testPointCount(): void
    {
        $series = new AdvancedScatterSeries();
        $series->setXValues([1, 2, 3, 4]);

        self::assertSame(4, $series->getPointCount());
    }

    public function testDataPointFillUsesUnderlyingDataPoint(): void
    {
        $series = new AdvancedScatterSeries('Test', [[1.0, 2.0], [3.0, 4.0]]);

        $fill = $series->getDataPointFill(1);
        $fill->setStartColor(new Color(Color::COLOR_RED));

        self::assertSame(Color::COLOR_RED, $series->getDataPointFill(1)->getStartColor()->getARGB());
        self::assertSame($fill, $series->getDataPoints()[1]->getFill());
        self::assertSame([1 => $fill], $series->getDataPointFills());
    }

    public function testDataPointFillRoundTripsViaDataPointObject(): void
    {
        $point = new DataPoint(1.0, 2.0);
        $point->setFill((new Fill())->setFillType(Fill::FILL_SOLID)->setStartColor(new Color(Color::COLOR_BLUE)));
        $series = new AdvancedScatterSeries('Test', [$point]);

        $fills = $series->getDataPointFills();
        self::assertArrayHasKey(0, $fills);
        self::assertSame(Color::COLOR_BLUE, $fills[0]->getStartColor()->getARGB());
    }

    public function testSetDataPointLabelCreatesPointIfMissing(): void
    {
        $series = new AdvancedScatterSeries();
        $series->setDataPointLabel(2, 'Surprise');

        self::assertSame(3, $series->getPointCount());
        self::assertSame('Surprise', $series->getDataPointLabel(2));
        self::assertNull($series->getDataPointLabel(0));
    }

    public function testGetDataPointLabelsReturnsOnlyTitledPoints(): void
    {
        $series = new AdvancedScatterSeries('s', [
            [1, 1, 'one'],
            [2, 2],            // no label
            [3, 3, 'three'],
        ]);

        self::assertSame([0 => 'one', 2 => 'three'], $series->getDataPointLabels());
    }

    public function testHashCodeReflectsData(): void
    {
        $a = new AdvancedScatterSeries('S', [[1.0, 2.0]]);
        $b = new AdvancedScatterSeries('S', [[1.0, 2.0]]);
        $c = new AdvancedScatterSeries('S', [[1.0, 3.0]]);

        self::assertSame($a->getHashCode(), $b->getHashCode());
        self::assertNotSame($a->getHashCode(), $c->getHashCode());
    }

    public function testHashCodeChangesWithLabel(): void
    {
        $a = new AdvancedScatterSeries('S', [[1.0, 2.0]]);
        $b = new AdvancedScatterSeries('S', [[1.0, 2.0, 'Apple']]);

        self::assertNotSame($a->getHashCode(), $b->getHashCode());
    }

    public function testCloneDeepCopiesPoints(): void
    {
        $series = new AdvancedScatterSeries('S', [[1.0, 2.0, 'A']]);
        $series->getDataPoints()[0]->setFont((new Font())->setSize(12));

        $clone = clone $series;
        $clone->getDataPoints()[0]->setTitle('Mutated');

        self::assertSame('A', $series->getDataPoints()[0]->getTitle());
        self::assertSame('Mutated', $clone->getDataPoints()[0]->getTitle());
        self::assertNotSame($series->getDataPoints()[0], $clone->getDataPoints()[0]);
    }

    public function testInheritsFromBaseSeries(): void
    {
        $series = new AdvancedScatterSeries();
        self::assertInstanceOf(Series::class, $series);
    }
}
