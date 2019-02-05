<?php declare(strict_types=1);

namespace TBPixel\XMLStreamer\Tests;

use PHPUnit\Framework\TestCase;

abstract class ReaderStreamTest extends TestCase
{
    /**
     * The file reader stream.
     *
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $stream;

    /**
     * Return a new stream implementation for testing.
     */
    abstract protected function newStream(): \Psr\Http\Message\StreamInterface;

    protected function setUp(): void
    {
        $this->stream = $this->newStream();
    }

    protected function tearDown(): void
    {
        $this->stream->close();
    }

    /** @test */
    public function can_read()
    {
        $this->assertTrue($this->stream->isReadable());

        $data = $this->stream->read(1024);

        $this->assertIsString($data);
        $this->assertNotEmpty($data);
    }

    /** @test */
    public function cannot_write()
    {
        $this->expectException(\RuntimeException::class);
        $this->assertFalse($this->stream->isWritable());
        $this->stream->write('foo');
    }

    /** @test */
    public function can_check_position_of_cursor()
    {
        $this->assertEquals(0, $this->stream->tell());
    }

    /** @test */
    public function can_seek_position_of_cursor()
    {
        $this->assertTrue($this->stream->isSeekable());

        $this->stream->seek(1, SEEK_SET); // set to 1
        $this->assertEquals(1, $this->stream->tell());

        $this->stream->seek(1, SEEK_CUR); // add 1 to current
        $this->assertEquals(2, $this->stream->tell());

        $this->stream->seek(0, SEEK_END); // set to end
        $this->assertEquals(59, $this->stream->tell());
    }

    /** @test */
    public function can_rewind_position_of_cursor()
    {
        $this->stream->seek(1);
        $this->stream->rewind();
        $this->assertEquals(0, $this->stream->tell());
    }

    /** @test */
    public function can_get_contents()
    {
        $actual = str_replace(["\n", ' '], '', $this->stream->getContents());
        $expected = file_get_contents(__DIR__ . '/assets/test-data.xml');

        if (!$expected) {
            throw new \RuntimeException("test data could not be loaded!");
        }

        $expected = str_replace(["\n", ' '], '', $expected);

        $this->assertStringContainsString($actual, $expected);
    }

    /** @test */
    public function can_get_meta()
    {
        $meta = $this->stream->getMetadata();
        $size = $this->stream->getMetadata('size');
        $fake = $this->stream->getMetadata('fake');

        $this->assertIsArray($meta);
        $this->assertNotEmpty($meta);
        $this->assertNotNull($size);
        $this->assertNull($fake);
    }
}
