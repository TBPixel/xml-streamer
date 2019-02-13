<?php

namespace TBPixel\XMLStreamer\Streams;

use TBPixel\XMLStreamer\Cursor;
use Psr\Http\Message\StreamInterface;

abstract class ReaderStream implements StreamInterface
{
    /**
     * The current position of the cursor.
     *
     * @var Cursor
     */
    protected $cursor;

    /**
     * The starting depth of the stream.
     *
     * If an integer is supplied the starting depth will iterate until it meets that depth; if a string is supplied then the reader will iterate until it finds that tag and set that to the depth.
     *
     * @var int|string
     */
    protected $depth;

    /**
     * The XML reader.
     *
     * @var \XMLReader
     */
    protected $reader;

    /**
     * Metadata about the stream.
     *
     * @var array
     */
    protected $meta = [];

    /**
     * Construct new FileReaderStream.
     *
     * @param int|string $depth The depth or starting tag of the reader stream
     */
    public function __construct($depth = 0)
    {
        $this->setDepth($depth);
        $this->reader = $this->newXMLReader();
        $this->cursor = $this->newCursor();
        $this->meta = $this->resolveMeta();
    }

    /**
     * Create a new XMLReader.
     *
     * @throws \RuntimeException
     */
    abstract protected function newXMLReader(): \XMLReader;

    /**
     * Closes the stream when the destructed
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        $this->rewind();

        return $this->reader->readString();
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        $this->reader->close();
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $this->reader->close();
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int Returns the size in bytes.
     */
    public function getSize()
    {
        $this->getMetadata('size');
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        return $this->cursor->position();
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return $this->cursor->position() >= $this->cursor->max();
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        switch ($whence) {
            case SEEK_SET:
                $this->cursor->set($offset);
                $this->reader->moveToFirstAttribute();
                break;
            case SEEK_CUR:
                $this->cursor->move($offset);
                break;
            case SEEK_END:
                $this->cursor->set($this->cursor->max() + $offset);
                $this->reader->moveToFirstAttribute();
                break;
        }

        for ($i = 0; $i < $this->cursor->position(); $i++) {
            $this->reader->next();
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        $this->cursor->reset();
        $this->reader->moveToFirstAttribute();
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        throw new \RuntimeException("FileReaderStream cannot write.");
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        $depth = 0;

        try {
            while ($this->reader->read()) {

                // Iterate nodes only
                if ($this->reader->nodeType !== \XMLReader::ELEMENT) {
                    continue;
                }

                // Set depth to int
                if (is_int($this->depth)) {
                    $depth = $this->depth;
                }

                // Iterate depth recursively until node found.
                if (is_string($this->depth) && $this->reader->name !== $this->depth) {
                    $depth++;
                    continue;
                }

                // Skip to content depth
                if ($this->reader->depth !== $depth) {
                    continue;
                }

                // Get next chunk and append byte length
                $xml = $this->reader->readOuterXML();

                // Return nothing if next chunk is larger than length.
                if (($length > 0) && strlen($xml) > $length) {
                    return '';
                }

                // Move to the next line.
                $this->reader->next();
                $this->cursor->forwards();

                return $xml;
            }
        } catch (\Throwable $err) {
            throw new \RuntimeException($err->getMessage(), $err->getCode(), $err);
        }

        return '';
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        $contents = '';

        while (!empty($data = $this->read(0))) {
            $contents .= $data;
        }

        return $contents;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if ($key) {
            return isset($this->meta[$key]) ? $this->meta[$key] : null;
        }

        return $this->meta;
    }

    /**
     * Sets the depth of the reader stream.
     *
     * @param int|string $depth
     *
     * @return void
     */
    protected function setDepth($depth)
    {
        if (!is_int($depth) && !is_string($depth)) {
            throw new \InvalidArgumentException('Reader depth must be either an integer or a string tag name, ' . gettype($depth) . ' given.');
        }

        if (is_string($depth)) {
            if (empty($depth)) {
                throw new \InvalidArgumentException('Reader depth cannot be an empty string!');
            }

            if (is_numeric($depth)) {
                $depth = (int) $depth;
                $depth = ($depth > 0) ? $depth : 0;
            }
        }

        $this->depth = $depth;
    }

    /**
     * Create a new cursor.
     */
    protected function newCursor(): Cursor
    {
        $reader = $this->newXMLReader();

        $maxPosition = 0;

        while ($reader->read()) {
            $maxPosition++;
        }

        $reader->close();

        return new Cursor($maxPosition);
    }

    /**
     * Resolves and returns the metadata info.
     */
    protected function resolveMeta(): array
    {
        $size = strlen(
            $this->reader->readOuterXML()
        );

        $this->rewind();

        return [
            'size' => $size,
        ];
    }
}
