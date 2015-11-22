<?php
/**
 * A level/xp bar component.
 *
 * @author Lemmmy
 */
class ComponentXPBar extends Component
{
	/**
	 * The width of this xp bar
	 *
	 * @var int
	 */
	private $width;

	/**
	 * The height of this xp bar
	 *
	 * @var int
	 */
	private $height;

	/**
	 * The rounding of this xp bar
	 *
	 * @var int
	 */
	private $rounding;

	/**
	 * The hex colour of this xp bar
	 *
	 * @var string
	 */
	private $hexColour;

	/**
	 * @param OsuSignature $signature The base signature
	 * @param int $x The X position of this xp bar
	 * @param int $y The Y position of this xp bar
	 * @param string $hexColour The colour of this xp bar
	 * @param int $width The width of this xp bar
	 * @param int $height The height of this xp bar
	 * @param int $rounding How much to round this xp bar
	 */
	public function __construct(
		OsuSignature $signature,
		$x = 0,
		$y = 0,
		$hexColour = "#ffa200",
		$width = 0,
		$height = 0,
		$rounding = 3) {

		parent::__construct($signature, $x, $y);

		$this->width = $width;
		$this->height = $height;

		$this->rounding = $rounding;

		$this->hexColour = $hexColour;
	}
	public function getWidth() {
		return $this->width;
	}

	public function getHeight() {
		return $this->height;
	}

	public function draw(OsuSignature $signature) {
		
	}
}