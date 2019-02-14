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
     * @param int|string $depth The depth or tag name to start iteration at, defaults to 0.
     */
    public function __construct(string $file, $depth = 0)
    {
        $this->file = $file;
        parent::__construct($depth);
    }

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

    protected function sizeInBytes(): int
    {
        $size = filesize($this->file);

        if (!$size) {
            return 0;
        }

        return $size;
    }
}
