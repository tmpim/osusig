<?php
/**
 * @author Lemmy
 */
class Template
{
	/**
	 * The list of components for this signature.
	 *
	 * @var array
	 */
	private $components = array();

	/**
	 * The signature that this is a template of.
	 *
	 * @var Signature
	 */
	private $signature;

	/**
	 * Extra padding to be added to the signature.
	 *
	 * @var int
	 */
	protected $extraWidth;

	/**
	 * Extra padding to be added to the signature.
	 *
	 * @var int
	 */
	protected $extraHeight;

	/**
	 * Add the components.
	 *
	 * @param OsuSignature $signature The base signature.
	 */
	public function __construct(OsuSignature $signature) {
		$this->signature = $signature;
	}

	/**
	 * This template's components.
	 *
	 * @return array
	 */
	public function getComponents() {
		return $this->components;
	}

	/**
	 * @return Signature The signature that this is a template of.
	 */
	public function getSignature()
	{
		return $this->signature;
	}

	/**
	 * Overridable method to determine whether this template should draw the triangle strip.
	 * @return bool Should the triangle strip be drawn for this template or not?
	 */
	public function hasTriangleStrip() {
		return true;
	}

	/**
	 * Calculates the required width of the template based on the components
	 * and their layout.
	 *
	 * @return int The width we calculated.
	 */
	public function calculateBaseWidth() {
		// if (isset($_GET['width']) && is_numeric($_GET['width']) && $_GET['width'] > 0) return $_GET['width']; // Debugging purposes

		$x1 = 0;
		$x2 = 0;

		foreach ($this->getComponents() as $component) {
			if ($component->x < $x1) $x1 = $component->x;
			if ($component->x + $component->getWidth() > $x2) $x2 = $component->x + $component->getWidth();
		}

		return $x2 - $x1 + $this->extraWidth;

		//return 330;
	}

	/**
	 * Calculates the required height of the template based on the components
	 * and their layout.
	 *
	 * @return int The height we calculated.
	 */
	public function calculateBaseHeight() {
		// if (isset($_GET['height']) && is_numeric($_GET['height']) && $_GET['height'] > 0) return $_GET['height']; // Debugging purposes

		$y1 = 0;
		$y2 = 0;

		foreach ($this->getComponents() as $component) {
			if ($component->y < $y1) $y1 = $component->y;
			if ($component->y + $component->getHeight() > $y2) $y2 = $component->y + $component->getHeight();
		}

		return $y2 - $y1 + $this->extraHeight;

		//return 86;
	}

	/**
	 * Adds a component to this template
	 *
	 * @param Component $component The component to add
	 */
	public function addComponent(Component $component) {
		array_push($this->components, $component);
	}

	/**
	 * Draws the template's components
	 */
	public function drawComponents() {
		foreach ($this->getComponents() as $component) {
			$component->draw($this->getSignature());
		}
	}
}