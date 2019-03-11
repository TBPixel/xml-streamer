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

    protected function setUp(): void
    {
        $stream = new FileReaderStream(__DIR__ . '/assets/3MB-test-data.xml', 'track');

        $this->client = new Client($stream, [
            'track' => TestTrack::class,
        ]);
    }

    protected function tearDown(): void
    {
        $this->client->close();
    }

    /** @test */
    public function ensure_low_memoy_usage()
    {
        $filesize = filesize(__DIR__ . '/assets/3MB-test-data.xml');
        $maxMemory = memory_get_usage() + $filesize;
        $peak = memory_get_peak_usage();
        $count = 0;

        foreach ($this->client->iterate() as $type) {
            // Allows us to check for totaly memory usage.
            $count += 1;
        }

        $this->assertLessThan($filesize, $maxMemory - $peak);
        $this->assertEquals(25000, $count);
    }
}
