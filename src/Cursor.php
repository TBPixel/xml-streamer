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
        $this->position = max(0, min($position, $this->max));

        return $this;
    }

    /**
     * Moves the cursor by the provided distance, relative to the current position. Negative numbers move backwards.
     */
    public function move(int $distance): self
    {
        $pos = $this->position + $distance;
        $range = $this->max + 1;

        if ($pos < 0) {
            $pos = -$pos % $range;
            $pos = $this->max - ($pos - 1);
        } elseif ($pos > $this->max) {
            $pos = $distance % $range;
        }

        return $this->set($pos);
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
