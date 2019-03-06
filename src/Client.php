<?php

namespace TBPixel\XMLStreamer;

use Psr\Http\Message\StreamInterface;

class Client implements \IteratorAggregate
{
    /**
     * The XML Stream.
     *
     * @var StreamInterface
     */
    protected $stream;

    /**
     * An array for the classmap to auto cast.
     *
     * @var array
     */
    protected $classmap;

    public function __construct(StreamInterface $stream, array $classmap = [])
    {
        $this->stream = $stream;
        $this->setClassmap($classmap);
    }

    /**
     * Iterates the stream data, mapping the retrieved data and yielding the result.
     *
     * @param int $length The maximum length in bytes to iterate. 0 or negative numbers will be interpretted as reading until the end of the stream.
     */
    public function iterate(int $length = 0): \Iterator
    {
        while (!empty($data = $this->stream->read($length))) {
            $xml = new \SimpleXMLElement($data);

            yield $this->cast($xml);
        }
    }

    /**
     * Closes the stream from the client.
     *
     * @return void
     */
    public function close()
    {
        $this->stream->close();
    }

    public function getIterator()
    {
        foreach ($this->iterate() as $value) {
            yield $value;
        }

        $this->stream->rewind();
    }

    /**
     * Casts a SimpleXMLElement to a given object by classname, or returns simple xml element if unavailable.
     */
    private function cast(\SimpleXMLElement $element): object
    {
        if (!isset($this->classmap[$element->getName()])) {
            return $element;
        }

        $class = $this->classmap[$element->getName()];

        return $class::fromSimpleXML($element);
    }

    /**
     * Validates and sets the classmap.
     *
     * @return void
     */
    private function setClassmap(array $classmap)
    {
        foreach ($classmap as $class) {
            if (!in_array($interface = CreateFromSimpleXML::class, class_implements($class))) {
                throw new \InvalidArgumentException("Class `{$class}` must implement `{$interface}`");
            }
        }

        $this->classmap = $classmap;
    }
}
