<?php

namespace TBPixel\XMLStreamer;

interface CreateFromSimpleXML
{
    /**
     * Create a new instance from a Simple XML Element.
     *
     * @return static
     */
    public static function fromSimpleXML(\SimpleXMLElement $element);
}
