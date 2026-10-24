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

namespace PhpOffice\PhpPresentation;

/**
 * A named group of slides, as PowerPoint shows them in its slide pane.
 *
 * A section holds slides of the presentation it belongs to; the order of the
 * slides is the presentation's, not the order they were added in.
 */
class Section
{
    /**
     * @var string
     */
    protected $name;

    /**
     * A GUID in braces, as PowerPoint writes it.
     *
     * @var string
     */
    protected $id;

    /**
     * @var array<int, Slide>
     */
    protected $slides = [];

    public function __construct(string $name = '', ?string $id = null)
    {
        $this->name = $name;
        $this->id = $id ?? self::generateId();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function addSlide(Slide $slide): self
    {
        if (!$this->hasSlide($slide)) {
            $this->slides[] = $slide;
        }

        return $this;
    }

    /**
     * @param array<int, Slide> $slides
     */
    public function setSlides(array $slides = []): self
    {
        $this->slides = [];

        foreach ($slides as $slide) {
            $this->addSlide($slide);
        }

        return $this;
    }

    /**
     * @return array<int, Slide>
     */
    public function getSlides(): array
    {
        return $this->slides;
    }

    public function hasSlide(Slide $slide): bool
    {
        return in_array($slide, $this->slides, true);
    }

    public function removeSlide(Slide $slide): self
    {
        $this->slides = array_values(array_filter($this->slides, function (Slide $held) use ($slide): bool {
            return $held !== $slide;
        }));

        return $this;
    }

    /**
     * A random (version 4) GUID, upper case and in braces.
     */
    public static function generateId(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr(ord($bytes[6]) & 0x0F | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3F | 0x80);

        return '{' . strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4))) . '}';
    }
}
