<?php

namespace TBPixel\XMLStreamer\Streams;

use TBPixel\XMLStreamer\Cursor;

class FileReaderStream extends ReaderStream
{
    /**
     * The absolute path to the file.
     *
     * @var string
     */
    protected $file;

    /**
     * Construct new FileReaderStream.
     *
     * @param string $file Absolute path to the file.
     */
    public function __construct(string $file, int $depth = 0)
    {
        $this->file = $file;
        parent::__construct($depth);
    }

    /**
     * Attempts to open a file with the reader, throwing a runtime exception if unable to.
     *
     * @throws \RuntimeException
     */
    protected function newXMLReader(): \XMLReader
    {
        try {
            $reader = new \XMLReader;

            if (!$reader->open($this->file)) {
                throw new \RuntimeException("Failed to open file `{$this->file}`");
            }

            return $reader;
        } catch (\Throwable $err) {
            throw new \RuntimeException($err->getMessage(), $err->getCode(), $err);
        }
    }
}
