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
		$rounding = 1) {

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
		$composite = new Imagick();
		$composite->newPseudoImage($this->getWidth(), $this->getHeight(), "canvas:transparent");

		// Background

		$draw = new ImagickDraw();
		$draw->setFillColor(new ImagickPixel("#555555"));
		$draw->rectangle(0, 0, $this->getWidth(), $this->getHeight());

		$composite->drawImage($draw);

		// Main bar

		$level = $signature->getUser()['level'];
		$xp = $level - floor($level);

		$draw = new ImagickDraw();
		$draw->setFillColor(new ImagickPixel($this->hexColour));
		$draw->rectangle(0, 0, $this->getWidth() * $xp, $this->getHeight());

		$composite->drawImage($draw);

		// Bar end glow

		$draw = new ImagickDraw();
		$draw->setFillColor(new ImagickPixel('#ffffff'));
		$draw->setFillOpacity(0.3);
		$draw->rectangle(($this->getWidth() * $xp) - $this->getHeight(), 0, $this->getWidth() * $xp, $this->getHeight());

		$composite->drawImage($draw);

		// Text draw & metrics

		$textDraw = new ImagickDraw();
		$textDraw->setFillColor(new ImagickPixel('#555555'));
		$textDraw->setFontSize(12);
		$textDraw->setFont(ComponentLabel::FONT_DIRECTORY . ComponentLabel::FONT_REGULAR);
		$textDraw->setGravity(Imagick::GRAVITY_NORTHWEST);

		$metrics = $composite->queryFontMetrics($textDraw, 'lv' . floor($level));

		// Text white bg

		$draw = new ImagickDraw();
		$draw->setFillColor(new ImagickPixel('#ffffff'));

		$draw->rectangle(
			($this->getWidth() - $metrics['textWidth']) / 2 - 2,
			0,
			($this->getWidth() + $metrics['textWidth']) / 2 + 1,
			$this->getHeight());

		$composite->drawImage($draw);

		// Rounding

		$roundMask = new Imagick();
		$roundMask->newPseudoImage($this->getWidth(), $this->getHeight(), "canvas:transparent");

		$draw = new ImagickDraw();
		$draw->setFillColor(new ImagickPixel("black"));
		$draw->roundRectangle(0, 0, $this->getWidth() - 1, $this->getHeight() - 1, $this->rounding, $this->rounding);

		$roundMask->drawImage($draw);
		$roundMask->setImageFormat('png');

		$composite->compositeImage(
			$roundMask,
			Imagick::COMPOSITE_DSTIN,
			0,
			0,
			Imagick::CHANNEL_ALPHA
		);

		$signature->getCanvas()->compositeImage($composite, Imagick::COMPOSITE_DEFAULT, $this->x, $this->y);

		// Level text

		$signature->getCanvas()->annotateImage(
			$textDraw,
			$this->x + ($this->getWidth() - $metrics['textWidth']) / 2,
			$this->y + ($this->getHeight() - $metrics['textHeight']) / 2 - 2,
			0,
			'lv' . floor($level));
	}
}