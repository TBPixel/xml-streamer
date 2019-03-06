<?php declare(strict_types = 1);

namespace TBPixel\XMLStreamer\Tests;

use PHPUnit\Framework\TestCase;
use TBPixel\XMLStreamer\Streams\ReaderStream;

abstract class ReaderStreamTest extends TestCase
{
    /**
     * The file reader stream.
     *
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $stream;

    /**
     * @var string
     */
    private $testDataString;

    /**
     * Return a new stream implementation for testing.
     */
    abstract protected function newStream(): \Psr\Http\Message\StreamInterface;

    protected function setUp(): void
    {
        $this->stream = $this->newStream();
        $this->testDataString = '
            <record>
                <id>1</id>
                <first_name>Celina</first_name>
                <last_name>Elgey</last_name>
                <email>celgey0@purevolume.com</email>
                <gender>Female</gender>
                <ip_address>26.110.129.53</ip_address>
            </record>
            <record>
                <id>2</id>
                <first_name>Theodor</first_name>
                <last_name>Blanch</last_name>
                <email>tblanch1@bloomberg.com</email>
                <gender>Male</gender>
                <ip_address>34.246.226.138</ip_address>
            </record>';
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
        $this->assertEquals($this->stream->getSize(), $this->stream->tell());
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

        $expected = str_replace(["\n", ' '], '', $this->testDataString);

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function can_convert_to_string()
    {
        $actual = str_replace(["\n", ' '], '', $this->stream->__toString());

        $expected = str_replace(["\n", ' '], '', $this->testDataString);

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function must_rewind_before_converting_to_string()
    {
        $this->stream->seek(0, SEEK_END);

        $actual = str_replace(["\n", ' '], '', $this->stream->__toString());

        $expected = str_replace(["\n", ' '], '', $this->testDataString);

        $this->assertEquals($expected, $actual);
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

    /** @test */
    public function can_close_stream()
    {
        $this->stream->close();

        $this->assertFalse($this->stream->isReadable());
        $this->assertFalse($this->stream->isSeekable());
        $this->assertFalse($this->stream->isWritable());
    }

    /** @test */
    public function cannot_seek_when_closed()
    {
        $this->expectException(\RuntimeException::class);
        $this->stream->close();
        $this->stream->seek(0);
    }

    /** @test */
    public function cannot_read_when_closed()
    {
        $this->expectException(\RuntimeException::class);
        $this->stream->close();
        $this->stream->read(0);
    }

    /** @test */
    public function cannot_read_when_detatched()
    {
        $this->expectException(\RuntimeException::class);
        $this->stream->detach();
        $this->stream->read(0);
    }

    /** @test */
    public function stream_will_close_when_destructured()
    {
        $this->stream->__destruct();

        $this->assertFalse($this->stream->isReadable());
        $this->assertFalse($this->stream->isSeekable());
        $this->assertFalse($this->stream->isWritable());
    }

    /** @test */
    public function can_determine_if_eof()
    {
        $this->stream->seek(0, SEEK_END);
        $this->assertTrue($this->stream->eof());

        $this->stream->seek(-1, SEEK_END);
        $this->assertFalse($this->stream->eof());
    }

    /** @test */
    public function default_depth_starts_at_root()
    {
        $stream = new class extends ReaderStream {
            protected function newXMLReader(): \XMLReader
            {
                $reader = new \XMLReader();

                $reader->open(__DIR__ . '/assets/test-data.xml');

                return $reader;
            }

            protected function sizeInBytes(): int
            {
                return 0;
            }
        };

        $testDataString = '
            <testdata>
                <record>
                    <id>1</id>
                    <first_name>Celina</first_name>
                    <last_name>Elgey</last_name>
                    <email>celgey0@purevolume.com</email>
                    <gender>Female</gender>
                    <ip_address>26.110.129.53</ip_address>
                </record>
                <record>
                    <id>2</id>
                    <first_name>Theodor</first_name>
                    <last_name>Blanch</last_name>
                    <email>tblanch1@bloomberg.com</email>
                    <gender>Male</gender>
                    <ip_address>34.246.226.138</ip_address>
                </record>
            </testdata>';


        $actual = str_replace(["\n", ' '], '', $stream->getContents());
        $expected = str_replace(["\n", ' '], '', $testDataString);

        $this->assertEquals($expected, $actual);
    }
}
