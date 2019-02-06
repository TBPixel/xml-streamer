<?php declare(strict_types=1);

namespace TBPixel\XMLStreamer\Tests\TestClasses;

use TBPixel\XMLStreamer\CreateFromSimpleXML;

final class TestRecord implements CreateFromSimpleXML
{
    public static function fromSimpleXML(\SimpleXMLElement $element)
    {
        return new static();
    }
}
