<?php declare(strict_types = 1);

namespace TBPixel\XMLStreamer\Tests;

use TBPixel\XMLStreamer\Streams\FileReaderStream;

final class NamedFileReaderStreamTest extends ReaderStreamTest
{
    protected function newStream(): \Psr\Http\Message\StreamInterface
    {
        return new FileReaderStream(__DIR__ . '/assets/test-data.xml', 'record');
    }
}
