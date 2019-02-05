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

    public function __construct(string $body, string $encoding = 'UTF-8', int $depth = 0)
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
}
