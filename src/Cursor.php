<?php

namespace TBPixel\XMLStreamer;

final class Cursor
{
    /**
     * The position of the cursor.
     *
     * @var int
     */
    private $position;

    /**
     * The maximum position of the cursor.
     *
     * @var int
     */
    private $max;

    public function __construct(int $max)
    {
        $this->position = 0;
        $this->max = $max;
    }

    /**
     * The current position of the cursor.
     */
    public function position(): int
    {
        return $this->position;
    }

    /**
     * The max position of the cursor.
     */
    public function max(): int
    {
        return $this->max;
    }

    /**
     * Reset the position of the cursor.
     */
    public function reset(): self
    {
        $this->position = 0;

        return $this;
    }

    /**
     * Sets the absolute position of the cursor.
     */
    public function set(int $position): self
    {
        if ($position < 0) {
            $this->position = $this->max + $position;
        } elseif ($position > $this->max) {
            $this->position = $this->max - $position;
        } else {
            $this->position = $position;
        }

        return $this;
    }

    /**
     * Moves the cursor by the provided distance, relative to the current position. Negative numbers move backwards.
     */
    public function move(int $distance): self
    {
        return $this->set($this->position + $distance);
    }

    /**
     * Move the cursor backwards relative to the current position.
     */
    public function backwards(int $distance = 1): self
    {
        return $this->move(-$distance);
    }

    /**
     * Move the cursor forwards relative to the current position.
     */
    public function forwards(int $distance = 1): self
    {
        return $this->move($distance);
    }
}
