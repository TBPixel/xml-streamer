<?php

namespace TBPixel\XMLStreamer\Streams;

use TBPixel\XMLStreamer\Cursor;

class StringReaderStream extends ReaderStream
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $encoding;

    /**
     * Construct a new StringReaderStream.
     *
     * @param string $body The XML string body.
     * @param string $encoding The encoding format to use, defaults to 'UTF-8'
     * @param int|string $depth The depth or tag name to start iteration at.
     */
    public function __construct(string $body, string $encoding = 'UTF-8', $depth = 0)
    {
        $this->body = $body;
        $this->encoding = $encoding;
        parent::__construct($depth);
    }

    protected function newXMLReader(): \XMLReader
    {
        try {
            $reader = new \XMLReader;

            if (!$reader->xml($this->body, $this->encoding)) {
                throw new \RuntimeException('Cannot read provided XML body.');
            }

            return $reader;
        } catch (\Throwable $err) {
            throw new \RuntimeException($err->getMessage(), $err->getCode(), $err);
        }
    }

    protected function sizeInBytes(): int
    {
        return strlen($this->body);
    }
}
