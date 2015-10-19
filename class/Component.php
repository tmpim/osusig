<?php
/**
 * @author Lemmy
 */
class Component
{
    /**
     * @var The X position of this component.
     */
    public $x;

    /**
     * @var The Y position of this component.
     */
    public $y;

    public function __construct($x = 0, $y = 0) {
        $this->x = $x;
        $this->y = $y;
    }

    public function getWidth() {
        return 0;
    }

    public function getHeight() {
        return 0;
    }
}