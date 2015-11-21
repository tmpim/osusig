<?php
/**
 * @author Lemmmy
 */
class Card
{
	/**
	 * @var Imagick The Imagick object for the canvas.
	 */
	protected $canvas;

	/**
	 * The width of the signature excluding the margin and stroke width
	 * calculated by the size of all components.
	 *
	 * @var int
	 */
	protected $baseWidth;

	/**
	 * The height of the signature excluding the margin and stroke width
	 * calculated by the size of all components.
	 *
	 * @var int
	 */
	protected $baseHeight;

	/**
	 * Draw the background to the canvas.
	 *
	 * @param $canvas Imagick The canvas to draw to
	 * @param $hexColour string Hexadecimal colour value
	 * @param $template Template The template we're drawing the card for
	 * @param $baseWidth int The calculated width of the components
	 * @param $baseHeight int The calculated height of the components
	 */
	public function draw($canvas, $hexColour, $template, $baseWidth, $baseHeight) {
		$this->canvas = $canvas;
		$this->baseWidth = $baseWidth;
		$this->baseHeight = $baseHeight;
	}
}