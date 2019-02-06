<?php declare(strict_types=1);

namespace TBPixel\XMLStreamer\Tests\TestClasses;

use TBPixel\XMLStreamer\CreateFromSimpleXML;

final class TestTrack implements CreateFromSimpleXML
{
    /** @var string */
    public $path;

    /** @var string */
    public $title;

    public function __construct(string $path, string $title)
    {
        $this->path = $path;
        $this->title = $title;
    }

    public static function fromSimpleXML(\SimpleXMLElement $element)
    {
        return new static(
            (string) $element->path,
            (string) $element->title
        );
    }
}
