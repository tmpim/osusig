<?php
/**
 * @author Lemmy
 */
class Component
{
    /**
     * @var The X position of this component.
     */
    private $x;

    /**
     * @var The Y position of this component.
     */
    private $y;

    public function __construct($x = 0, $y = 0) {
        $this->x = $x;
        $this->y = $y;
    }

    public function getWidth() {

    }

    public function getHeight() {

    }
}