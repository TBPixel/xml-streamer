<?php declare(strict_types = 1);

namespace TBPixel\XMLStreamer\Tests;

use TBPixel\XMLStreamer\Streams\FileReaderStream;

final class FileReaderStreamTest extends ReaderStreamTest
{
    protected function newStream(): \Psr\Http\Message\StreamInterface
    {
        return new FileReaderStream(__DIR__ . '/assets/test-data.xml', 1);
    }

    /** @test */
    public function cannot_open_non_existent_file()
    {
        $this->expectException(\RuntimeException::class);

        new FileReaderStream('foobar');
    }

    /** @test */
    public function will_return_zero_if_unknown_file_size()
    {
        $this->expectException(\RuntimeException::class);

        $stream = new FileReaderStream('foobar');

        $this->assertEquals(0, $stream->getSize());
    }

    /** @test */
    public function default_depth_starts_at_root()
    {
        $stream = new FileReaderStream(__DIR__ . '/assets/test-data.xml');

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
