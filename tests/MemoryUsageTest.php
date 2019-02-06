<?php declare(strict_types=1);

namespace TBPixel\XMLStreamer\Tests;

use PHPUnit\Framework\TestCase;
use TBPixel\XMLStreamer\Client;
use TBPixel\XMLStreamer\Streams\FileReaderStream;
use TBPixel\XMLStreamer\Tests\TestClasses\TestTrack;

final class MemoryUsageTest extends TestCase
{
    /**
     * The XML parser client.
     *
     * @var Client
     */
    private $client;

    /**
     * The maximum allowed memory usage.
     *
     * @var int
     */
    private $maxMemory;

    protected function setUp(): void
    {
        $stream = new FileReaderStream(__DIR__ . '/assets/3MB-test-data.xml', 1);

        $this->client = new Client($stream, [
            'track' => TestTrack::class,
        ]);

        $this->maxMemory = memory_get_usage() + filesize(__DIR__ . '/assets/3MB-test-data.xml');
    }

    protected function tearDown(): void
    {
        $this->client->close();
    }

    /** @test */
    public function ensure_low_memoy_usage()
    {
        foreach ($this->client->iterate() as $type) {
            // Allows us to check for totaly memory usage.
        }

        $this->assertLessThanOrEqual($this->maxMemory, memory_get_peak_usage());
    }
}
