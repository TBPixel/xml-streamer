<?php declare(strict_types = 1);

namespace TBPixel\XMLStreamer\Tests;

use TBPixel\XMLStreamer\Cursor;
use PHPUnit\Framework\TestCase;

final class CursorTest extends TestCase
{
    /**
     * @var \TBPixel\XMLStreamer\Cursor
     */
    private $cursor;

    /**
     * @var int
     */
    private $max;

    protected function setUp(): void
    {
        $this->max = 10;
        $this->cursor = new Cursor($this->max);
    }

    /** @test */
    public function can_get_max_position()
    {
        $this->assertEquals($this->max, $this->cursor->max());
    }

    /** @test */
    public function can_set_position()
    {
        $this->cursor->set(1);
        $this->assertEquals(1, $this->cursor->position());
    }

    /** @test */
    public function cannot_set_beyond_range()
    {
        $this->cursor->set(11);
        $this->assertEquals($this->max, $this->cursor->position());

        $this->cursor->set(-1);
        $this->assertEquals(0, $this->cursor->position());
    }

    /** @test */
    public function can_move_position_relatively()
    {
        $this->cursor->move(1);
        $this->assertEquals(1, $this->cursor->position());

        $this->cursor->move(-1);
        $this->assertEquals(0, $this->cursor->position());
    }

    /** @test */
    public function can_wrap_position()
    {
        $this->cursor->move(11);
        $this->assertEquals(0, $this->cursor->position());

        $this->cursor->move(-1);
        $this->assertEquals($this->max, $this->cursor->position());
    }

    /** @test */
    public function can_move_position_backwards()
    {
        $this->cursor->backwards();
        $this->assertEquals($this->max, $this->cursor->position());

        $this->cursor->reset();
        $this->cursor->backwards(2);
        $this->assertEquals(9, $this->cursor->position());
    }

    /** @test */
    public function can_move_position_forwards()
    {
        $this->cursor->forwards();
        $this->assertEquals(1, $this->cursor->position());

        $this->cursor->reset();
        $this->cursor->forwards(11);
        $this->assertEquals(0, $this->cursor->position());
    }
}
