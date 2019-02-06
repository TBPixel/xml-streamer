<?php declare(strict_types=1);

namespace TBPixel\XMLStreamer\Tests;

use PHPUnit\Framework\TestCase;
use TBPixel\XMLStreamer\Client;
use TBPixel\XMLStreamer\Streams\FileReaderStream;
use TBPixel\XMLStreamer\Tests\TestClasses\TestRecord;

final class ClientTest extends TestCase
{
    /**
     * The data stream to work with.
     *
     * @var \Psr\Http\Message\StreamInterface
     */
    private $stream;

    protected function setUp(): void
    {
        $this->stream = new FileReaderStream(__DIR__ . '/assets/test-data.xml', 1);
    }

    protected function tearDown(): void
    {
        $this->stream->close();
    }

    /** @test */
    public function can_iterate_test_data()
    {
        $client = new Client($this->stream);

        foreach ($client->iterate() as $type) {
            $this->assertInstanceOf(\SimpleXMLElement::class, $type);
        }
    }

    /** @test */
    public function can_map_records_to_test_data()
    {
        $client = new Client($this->stream, [
            'record' => TestRecord::class,
        ]);

        foreach ($client->iterate() as $type) {
            $this->assertInstanceOf(TestRecord::class, $type);
        }
    }
}
