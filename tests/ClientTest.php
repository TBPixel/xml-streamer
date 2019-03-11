<?php declare(strict_types = 1);

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
        $items = [];
        $client = new Client($this->stream);

        foreach ($client->iterate() as $type) {
            $this->assertInstanceOf(\SimpleXMLElement::class, $type);

            $items[] = $type;
        }

        $this->assertNotEmpty($items);
        $this->assertEquals(1, (int)$items[0]->id);
    }

    /** @test */
    public function can_loop_test_data()
    {
        $items = [];
        $client = new Client($this->stream);

        foreach ($client as $type) {
            $this->assertInstanceOf(\SimpleXMLElement::class, $type);

            $items[] = $type;
        }

        $this->assertNotEmpty($items);
        $this->assertEquals(1, (int)$items[0]->id);
    }

    /** @test */
    public function can_count_test_data()
    {
        $client = new Client($this->stream);
        $this->assertCount(2, $client);
    }

    /** @test */
    public function can_map_records_to_test_data()
    {
        $items = [];
        $client = new Client($this->stream, [
            'record' => TestRecord::class,
        ]);

        foreach ($client->iterate() as $record) {
            $this->assertInstanceOf(TestRecord::class, $record);

            $items[] = $record;
        }

        $this->assertNotEmpty($items);
        $this->assertEquals(1, $items[0]->id);
        $this->assertInstanceOf(TestRecord::class, $items[0]);
    }

    /** @test */
    public function classmapped_classes_must_impement_createfromsimplexml()
    {
        $this->expectException(\InvalidArgumentException::class);

        $client = new Client($this->stream, [
            'record' => \stdClass::class,
        ]);
    }

    /** @test */
    public function stream_will_rewind_after_iteration()
    {
        for ($i = 0; $i < 2; $i++) {
            $this->can_loop_test_data();
        }
    }

    /** @test */
    public function can_close_stream()
    {
        $client = new Client($this->stream);
        $client->close();

        $this->assertFalse($this->stream->isReadable());
        $this->assertFalse($this->stream->isSeekable());
        $this->assertFalse($this->stream->isWritable());
    }
}
