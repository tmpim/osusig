<?php
/**
 * @author Lemmmy
 */
class Component
{
	/**
	 * The X position of this component.
	 *
	 * @var int
	 */
	public $x;

	/**
	 * The Y position of this component.
	 * @var int
	 */
	public $y;

	/**
	 * Initializes this component.
	 *
	 * @param OsuSignature $signature The base signature
	 * @param int $x The X position of this component
	 * @param int $y The Y position of this component
	 */
	public function __construct(OsuSignature $signature, $x = 0, $y = 0) {
		$this->x = $x;
		$this->y = $y;
	}

	/**
	 * @return int The width of this component. Overridable.
	 */
	public function getWidth() {
		return 0;
	}

	/**
	 * @return int The height of this component. Overridable.
	 */
	public function getHeight() {
		return 0;
	}

	/**
	 * Draws this component to the signature's canvas.
	 *
	 * @param OsuSignature $signature The signature to draw to.
	 */
	public function draw(OsuSignature $signature) {

	}
}