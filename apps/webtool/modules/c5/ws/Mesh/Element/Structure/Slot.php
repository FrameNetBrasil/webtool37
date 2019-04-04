<?php
namespace Mesh\Element\Structure;

class Slot
{
    private $bits;
    private $max;
    private $min;

    public function __construct($value = 0)
    {
        $this->bits = $value;
        $this->max = 0;
        $this->min = 10000;
    }

    public function getValue()
    {
        return $this->bits;
    }

    public function setValue($value) {
        $this->bits = $value;
    }

    public function max() {
        return $this->max;
    }

    public function min() {
        return $this->min;
    }

    function get($offset)
    {
        $mask = 1 << $offset;
        return ($mask & $this->bits) == $mask;
    }

    function set($offset)
    {
        if ($offset > $this->max) {
            $this->max = $offset;
        }
        if ($offset < $this->min) {
            $this->min = $offset;
        }
        $this->bits |= 1 << $offset;
        return $this;
    }

    function reset($offset)
    {
        $this->bits &= ~(1 << $offset);
        return $this;
    }

    function toggle($offset)
    {
        $this->bits ^= 1 << $offset;
        return $this->get($offset);
    }

    function merge($slot) {
        if ($slot->max() > $this->max) {
            $this->max = $slot->max();
        }
        if ($slot->min() < $this->min) {
            $this->min = $slot->min();
        }
        $this->bits |= $slot->getValue();
    }

    function _and($slot) {
        return $this->bits & $slot->getValue();
    }

    function toString () {
        return strrev(str_pad(base_convert($this->bits, 10, 2), 10, "0", STR_PAD_LEFT));
    }
}

