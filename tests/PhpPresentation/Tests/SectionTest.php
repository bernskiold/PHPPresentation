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

namespace PhpOffice\PhpPresentation\Tests;

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Section;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PhpOffice\PhpPresentation\Section
 */
class SectionTest extends TestCase
{
    public function testConstruct(): void
    {
        $section = new Section('Ford');

        self::assertSame('Ford', $section->getName());
        self::assertMatchesRegularExpression('/^\{[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}\}$/', $section->getId());
        self::assertSame([], $section->getSlides());
    }

    public function testGeneratedIdsDiffer(): void
    {
        self::assertNotSame((new Section())->getId(), (new Section())->getId());
    }

    public function testNameAndId(): void
    {
        $section = new Section('Ford', '{4E71D45A-3930-DB41-B97F-E963124E6882}');
        self::assertSame('{4E71D45A-3930-DB41-B97F-E963124E6882}', $section->getId());

        self::assertInstanceOf(Section::class, $section->setName('Coca-Cola'));
        self::assertSame('Coca-Cola', $section->getName());

        self::assertInstanceOf(Section::class, $section->setId('{CCF4504A-CB60-3845-9616-D6D3F915E927}'));
        self::assertSame('{CCF4504A-CB60-3845-9616-D6D3F915E927}', $section->getId());
    }

    public function testSlides(): void
    {
        $presentation = new PhpPresentation();
        $first = $presentation->getSlide(0);
        $second = $presentation->createSlide();
        $section = new Section('Ford');

        $section->addSlide($first)->addSlide($second)->addSlide($first);
        self::assertSame([$first, $second], $section->getSlides());
        self::assertTrue($section->hasSlide($second));

        $section->removeSlide($first);
        self::assertSame([$second], $section->getSlides());
        self::assertFalse($section->hasSlide($first));

        $section->setSlides([$first, $first]);
        self::assertSame([$first], $section->getSlides());
    }
}
