<?php declare(strict_types = 1);

namespace TBPixel\XMLStreamer\Tests;

use TBPixel\XMLStreamer\Streams\FileReaderStream;

final class TagDepthTest extends ReaderStreamTest
{
    protected function newStream(): \Psr\Http\Message\StreamInterface
    {
        return new FileReaderStream(__DIR__ . '/assets/test-data.xml', 'record');
    }

    /** @test */
    public function stream_read_will_return_string()
    {
        $stream = new FileReaderStream(__DIR__ . '/assets/test-data.xml', 'fake-tag');
        $empty = $stream->read(0);

        $this->assertIsString($empty);
        $this->assertEmpty($empty);
    }
}
