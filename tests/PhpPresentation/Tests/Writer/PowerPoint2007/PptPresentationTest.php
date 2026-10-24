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

namespace PhpPresentation\Tests\Writer\PowerPoint2007;

use PhpOffice\PhpPresentation\Section;
use PhpOffice\PhpPresentation\Tests\PhpPresentationTestCase;

class PptPresentationTest extends PhpPresentationTestCase
{
    protected $writerName = 'PowerPoint2007';

    public function testRender(): void
    {
        $this->assertZipFileExists('ppt/presentation.xml');
        $this->assertIsSchemaECMA376Valid();
    }

    public function testNoSections(): void
    {
        $this->assertZipXmlElementNotExists('ppt/presentation.xml', '/p:presentation/p:extLst');
        $this->assertIsSchemaECMA376Valid();
    }

    public function testSections(): void
    {
        $first = $this->oPresentation->getSlide(0);
        $second = $this->oPresentation->createSlide();
        $third = $this->oPresentation->createSlide();

        $this->oPresentation->createSection('Default Section')->addSlide($first);
        $this->oPresentation->addSection(new Section('Johnson & Johnson', '{722061EA-F044-FC4D-BA8A-A4503FFEED82}'))
            ->addSlide($third)
            ->addSlide($second);
        $this->oPresentation->createSection('Coca-Cola');

        $element = '/p:presentation/p:extLst/p:ext[@uri="{521415D9-36F7-43E2-AB2F-B90AF26B5E84}"]/p14:sectionLst/p14:section';
        $this->assertZipXmlElementCount('ppt/presentation.xml', $element, 3);
        $this->assertZipXmlAttributeEquals('ppt/presentation.xml', $element . '[1]', 'name', 'Default Section');
        $this->assertZipXmlAttributeEquals('ppt/presentation.xml', $element . '[1]/p14:sldIdLst/p14:sldId', 'id', '256');
        $this->assertZipXmlAttributeEquals('ppt/presentation.xml', $element . '[2]', 'name', 'Johnson & Johnson');
        $this->assertZipXmlAttributeEquals('ppt/presentation.xml', $element . '[2]', 'id', '{722061EA-F044-FC4D-BA8A-A4503FFEED82}');
        $this->assertZipXmlAttributeEquals('ppt/presentation.xml', $element . '[2]/p14:sldIdLst/p14:sldId[1]', 'id', '257');
        $this->assertZipXmlAttributeEquals('ppt/presentation.xml', $element . '[2]/p14:sldIdLst/p14:sldId[2]', 'id', '258');
        $this->assertZipXmlAttributeEquals('ppt/presentation.xml', $element . '[3]', 'name', 'Coca-Cola');
        $this->assertZipXmlElementCount('ppt/presentation.xml', $element . '[3]/p14:sldIdLst/p14:sldId', 0);
        $this->assertIsSchemaECMA376Valid();
    }

    public function testSectionIdsMatchTheSlideList(): void
    {
        $this->oPresentation->createSlide();
        $this->oPresentation->createSection('Ford')->addSlide($this->oPresentation->getSlide(1));

        $this->assertZipXmlAttributeEquals('ppt/presentation.xml', '/p:presentation/p:sldIdLst/p:sldId[2]', 'id', '257');
        $this->assertZipXmlAttributeEquals('ppt/presentation.xml', '/p:presentation/p:extLst/p:ext/p14:sectionLst/p14:section/p14:sldIdLst/p14:sldId', 'id', '257');
    }

    public function testASlideIsListedOnce(): void
    {
        $slide = $this->oPresentation->getSlide(0);
        $this->oPresentation->createSection('Ford')->addSlide($slide);
        $this->oPresentation->createSection('Coca-Cola')->addSlide($slide);

        $element = '/p:presentation/p:extLst/p:ext/p14:sectionLst/p14:section';
        $this->assertZipXmlElementCount('ppt/presentation.xml', $element . '[1]/p14:sldIdLst/p14:sldId', 1);
        $this->assertZipXmlElementCount('ppt/presentation.xml', $element . '[2]/p14:sldIdLst/p14:sldId', 0);
    }

    public function testASlideNoLongerInThePresentationIsNotListed(): void
    {
        $removed = $this->oPresentation->createSlide();
        $section = $this->oPresentation->createSection('Ford');
        $section->addSlide($removed);
        $this->oPresentation->setAllSections([$section]);
        $this->oPresentation->removeSlideByIndex(1);
        $section->addSlide($removed);

        $this->assertZipXmlElementCount('ppt/presentation.xml', '/p:presentation/p:extLst/p:ext/p14:sectionLst/p14:section/p14:sldIdLst/p14:sldId', 0);
    }
}
