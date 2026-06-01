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

namespace PhpOffice\PhpPresentation\Tests\Shape\Chart\Type;

use PhpOffice\PhpPresentation\Shape\Chart\Series\AdvancedScatterSeries;
use PhpOffice\PhpPresentation\Shape\Chart\Type\AdvancedScatter;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PhpOffice\PhpPresentation\Shape\Chart\Type\AdvancedScatter
 */
class AdvancedScatterTest extends TestCase
{
    public function testDefaults(): void
    {
        $object = new AdvancedScatter();

        self::assertSame(AdvancedScatter::STYLE_MARKER, $object->getScatterStyle());
        self::assertFalse($object->hasVaryColors());
        self::assertFalse($object->isSmooth());
        self::assertTrue($object->hasAxisX());
        self::assertTrue($object->hasAxisY());
        self::assertTrue($object->isXAxisValueAxis());
        self::assertEmpty($object->getSeries());
    }

    public function testScatterStyle(): void
    {
        $object = new AdvancedScatter();

        self::assertInstanceOf(AdvancedScatter::class, $object->setScatterStyle(AdvancedScatter::STYLE_LINE_MARKER));
        self::assertSame(AdvancedScatter::STYLE_LINE_MARKER, $object->getScatterStyle());

        $object->setScatterStyle(AdvancedScatter::STYLE_SMOOTH);
        self::assertSame(AdvancedScatter::STYLE_SMOOTH, $object->getScatterStyle());

        // Invalid style is ignored.
        $object->setScatterStyle('not-a-real-style');
        self::assertSame(AdvancedScatter::STYLE_SMOOTH, $object->getScatterStyle());
    }

    public function testVaryColors(): void
    {
        $object = new AdvancedScatter();

        self::assertFalse($object->hasVaryColors());
        self::assertInstanceOf(AdvancedScatter::class, $object->setVaryColors(true));
        self::assertTrue($object->hasVaryColors());

        $object->setVaryColors(false);
        self::assertFalse($object->hasVaryColors());
    }

    public function testSeries(): void
    {
        $object = new AdvancedScatter();

        self::assertInstanceOf(AdvancedScatter::class, $object->addSeries(new AdvancedScatterSeries()));
        self::assertCount(1, $object->getSeries());

        $object->setSeries([new AdvancedScatterSeries(), new AdvancedScatterSeries()]);
        self::assertCount(2, $object->getSeries());
    }

    public function testHashCodeChangesWithStyle(): void
    {
        $object = new AdvancedScatter();
        $hashBefore = $object->getHashCode();

        $object->setScatterStyle(AdvancedScatter::STYLE_LINE_MARKER);
        self::assertNotSame($hashBefore, $object->getHashCode());
    }

    public function testHashCodeChangesWithVaryColors(): void
    {
        $object = new AdvancedScatter();
        $hashBefore = $object->getHashCode();

        $object->setVaryColors(true);
        self::assertNotSame($hashBefore, $object->getHashCode());
    }
}
