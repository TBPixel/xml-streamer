<?php declare(strict_types = 1);

namespace TBPixel\XMLStreamer\Tests;

use PHPUnit\Framework\TestCase;
use TBPixel\XMLStreamer\Streams\StringReaderStream;

final class StringReaderStreamTest extends ReaderStreamTest
{
    /**
     * @var string
     */
    private $body;

    protected function newStream(): \Psr\Http\Message\StreamInterface
    {
        $body = file_get_contents(__DIR__ . '/assets/test-data.xml');

        if (!$body) {
            throw new \RuntimeException("test data could not be loaded!");
        }

        $this->body = $body;

        return new StringReaderStream($this->body, 'UTF-8', 1);
    }

    /** @test */
    public function default_depth_starts_at_root()
    {
        $stream = new StringReaderStream($this->body);

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
