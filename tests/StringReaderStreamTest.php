<?php declare(strict_types=1);

namespace TBPixel\XMLStreamer\Tests;

use PHPUnit\Framework\TestCase;
use TBPixel\XMLStreamer\Streams\StringReaderStream;

final class StringReaderStreamTest extends ReaderStreamTest
{
    protected function newStream(): \Psr\Http\Message\StreamInterface
    {
        $body = file_get_contents(__DIR__ . '/assets/test-data.xml');

        if (!$body) {
            throw new \RuntimeException("test data could not be loaded!");
        }

        return new StringReaderStream($body, 'UTF-8', 1);
    }
}
