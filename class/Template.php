<?php
/**
 * A template for signatures. This predefines the background {@link Card} and {@link Component}s.
 *
 * @author Lemmmy
 * @see Card
 * @see Component
 * @see TemplateNormal
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
	 * The Card used to draw the background of the signature.
	 *
	 * @var Card
	 */
	private $card;

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
	public function getSignature() {
		return $this->signature;
	}

	/**
	 * @return Card The background card of this signature template.
	 */
	public function getCard() {
		return $this->card;
	}

	/**
	 * Sets the background card of this signature template.
	 *
	 * @param Card $card The card to set to.
	 */
	public function setCard($card) {
		$this->card = $card;
	}

	/**
	 * @return int The width to be added to the image.
	 */
	public function getImageMarginWidth() {
		return 0;
	}

	/**
	 * @return int The height to be added to the image.
	 */
	public function getImageMarginHeight() {
		return 0;
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

		/** @var Component $component */
		foreach ($this->getComponents() as $component) {
			if (!$component->usesSpace) continue;
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
			if (!$component->usesSpace) continue;
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