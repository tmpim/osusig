<?php
/**
 * @author Lemmmy
 */
class OsuSignature extends Signature
{
	/**
	 * The margin of the entire signature card
	 */
	const SIG_MARGIN = 3;

	/**
	 * The outer stroke width of the signature card
	 */
	const SIG_STROKE_WIDTH = 2;

	/**
	 * The inner padding of the signature card
	 */
	const SIG_INNER_PADDING = 6;

	/**
	 * How much to round the edges of the signature card
	 */
	const SIG_ROUNDING = 3;

	/**
	 * How much to round the outer edges of the signature card
	 */
	const SIG_OUTER_ROUNDING = 3;

	/**
	 * How large the triangle strip should be
	 */
	const TRIANGLE_STRIP_HEIGHT = 28;

	/**
	 * The triangles image to use for the signature card's header
	 */
	const IMG_TRIANGLES = "img/triangles_all.png";


	/**
	 * The user data of the requested signature's subject
	 *
	 * @var array
	 */
	private $user;

	/**
	 * The width of the signature excluding the margin and stroke width
	 * calculated by the size of all components.
	 *
	 * @var int
	 */
	private $baseWidth;

	/**
	 * The height of the signature excluding the margin and stroke width
	 * calculated by the size of all components.
	 *
	 * @var int
	 */
	private $baseHeight;

	/**
	 * The template this signature will use for its components.
	 *
	 * @var Template
	 */
	private $template;

	/**
	 * Creates a new osu! signature.
	 *
	 * @param array $user The user whom the signature will be the signature's subject
	 * @param Template $template The template this signature will be based on.
	 */
	public function __construct($user, $template) {
		$this->user = $user;
		$this->template = new $template($this);

		$width = $this->template->calculateBaseWidth() + (self::SIG_MARGIN * 2);
		$height = $this->template->calculateBaseHeight() + (self::SIG_MARGIN * 2);

		parent::__construct($width, $height);
	}

	/**
	 * @return array The user data of the requested signature's subject
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Draws the background colour with triangles for the signature
	 *
	 * @param string $hexColour Hexadecimal colour value for the whole card
	 */
	public function drawBackground($hexColour) {
		$width = $this->canvas->getImageWidth();
		$height = $this->canvas->getImageHeight();

		$base = new Imagick();
		$base->newImage($width, $height, new ImagickPixel('transparent'));

		// The background layer - this draws the fill
		$this->drawBackgroundFill($base, $hexColour);

		// Composite the base onto the canvas at margin, margin
		$this->canvas->compositeImage($base, Imagick::COMPOSITE_DEFAULT, 0, 0);
	}

	/**
	 * Draws the background's fill for the signature
	 *
	 * @param Imagick $base The base to draw on
	 * @param string $hexColour Hexadecimal colour value for the whole card
	 */
	public function drawBackgroundFill($base, $hexColour) {
		$background = new ImagickDraw();
		$background->setFillColor($hexColour);
		$background->roundRectangle(
			self::SIG_MARGIN + (self::SIG_STROKE_WIDTH / 2),
			self::SIG_MARGIN + (self::SIG_STROKE_WIDTH / 2),
			($this->baseWidth + self::SIG_MARGIN - 1) - (self::SIG_STROKE_WIDTH / 2),
			($this->baseHeight + self::SIG_MARGIN - 1) - (self::SIG_STROKE_WIDTH / 2),
			self::SIG_OUTER_ROUNDING * 2,
			self::SIG_OUTER_ROUNDING * 2);

		$base->drawImage($background);
	}

	/**
	 * Draws the triangle strip for the signature
	 *
	 * @param string $hexColour Hexadecimal colour value for the whole card
	 */
	public function drawTriangleStrip($hexColour) {
		// The base for the triangles strip, to be drawn over the plain
		$backArea = new ImagickDraw();
		$backArea->setFillColor(new ImagickPixel($hexColour));
		$backArea->rectangle(
			self::SIG_MARGIN + self::SIG_STROKE_WIDTH,
			self::SIG_MARGIN + self::SIG_STROKE_WIDTH + 1,
			$this->baseWidth - self::SIG_STROKE_WIDTH + (self::SIG_STROKE_WIDTH / 2) + 1,
			(self::TRIANGLE_STRIP_HEIGHT - self::SIG_STROKE_WIDTH) + (self::SIG_ROUNDING * 4)
		);

		$this->canvas->drawImage($backArea);

		$originalTriangles = new Imagick(self::IMG_TRIANGLES);
		$originalTriangles->cropImage(
			$this->baseWidth, $this->baseHeight,
			$this->baseWidth / 2, $this->baseHeight / 2);

		$triangles = new Imagick();
		$triangles->newImage(
			$this->baseWidth - (self::SIG_STROKE_WIDTH * 2) - 2,
			self::TRIANGLE_STRIP_HEIGHT + self::SIG_STROKE_WIDTH,
			new ImagickPixel($hexColour));
		$triangles = $triangles->textureImage($originalTriangles);

		// The gradient to draw over the triangles
		$trianglesGradient1 = new Imagick();
		$trianglesGradient1->newPseudoImage(
			$this->baseWidth - (self::SIG_STROKE_WIDTH * 2) - 2,
			self::TRIANGLE_STRIP_HEIGHT + self::SIG_STROKE_WIDTH,
			'gradient:' . 'none' . '-' . $hexColour);
		$trianglesGradient1->setImageOpacity(0.6);

		// The second gradient to draw over the triangles
		$trianglesGradient2 = new Imagick();
		$trianglesGradient2->newPseudoImage(
			$this->baseWidth - (self::SIG_STROKE_WIDTH * 2) - 2,
			self::TRIANGLE_STRIP_HEIGHT + self::SIG_STROKE_WIDTH,
			'gradient:' . '#4a4a4a' . '-' . '#313131');

		// Composite the black and white gradient onto the triangles
		$triangles->compositeImage(
			$trianglesGradient2,
			Imagick::COMPOSITE_OVERLAY,
			0,
			0);

		$triangles->setImageOpacity(0.1);

		// Composite the triangles onto the base
		$this->canvas->compositeImage(
			$triangles,
			Imagick::COMPOSITE_DEFAULT,
			self::SIG_MARGIN + self::SIG_STROKE_WIDTH + 1,
			self::SIG_MARGIN + self::SIG_STROKE_WIDTH * 1.5);

		// Composite the triangles gradient onto the base
		$this->canvas->compositeImage($trianglesGradient1,
			Imagick::COMPOSITE_DEFAULT,
			self::SIG_MARGIN + self::SIG_STROKE_WIDTH + 1,
			self::SIG_MARGIN + self::SIG_STROKE_WIDTH * 1.5);
	}

	/**
	 * Draws the shadow of the card
	 */
	public function drawShadow() {
		$shadow = new Imagick();
		$shadow->newImage(
			$this->canvas->getImageWidth(),
			$this->canvas->getImageHeight(),
			new ImagickPixel('transparent'));
		$shadow->setImageBackgroundColor(new ImagickPixel('black'));

		$shadowArea = new ImagickDraw();
		$shadowArea->setFillColor(new ImagickPixel('black'));
		$shadowArea->roundRectangle(
			0,
			0,
			$this->baseWidth - 1,
			$this->baseHeight - 2,
			self::SIG_ROUNDING,
			self::SIG_ROUNDING
		);

		$shadow->drawImage($shadowArea);
		$shadow->shadowImage(15, 1.5, 0, 0);

		$this->canvas->compositeImage($shadow, Imagick::COMPOSITE_DEFAULT, 0, 1);
	}

	/**
	 * Draws the white area of the card
	 */
	public function drawPlainArea() {
		$plainArea = new ImagickDraw();
		$plainArea->setFillColor("white");
		$plainArea->roundRectangle(
			self::SIG_MARGIN + self::SIG_STROKE_WIDTH + 1,
			self::SIG_MARGIN + self::SIG_STROKE_WIDTH + self::TRIANGLE_STRIP_HEIGHT,
			$this->baseWidth - self::SIG_STROKE_WIDTH + (self::SIG_STROKE_WIDTH / 2) + 1,
			$this->baseHeight - self::SIG_STROKE_WIDTH + (self::SIG_STROKE_WIDTH / 2) + 1,
			self::SIG_ROUNDING,
			self::SIG_ROUNDING
		);

		$this->canvas->drawImage($plainArea);
	}

	/**
	 * Draws the stroke over the whole card
	 *
	 * @param string $hexColour [Hexadecimal colour value for the card stroke]
	 */
	public function drawFinalStroke($hexColour) {
		$cardStrokeImage = new Imagick();
		$cardStrokeImage->newPseudoImage($this->getCanvas()->getImageWidth(), $this->getCanvas()->getImageHeight(), 'canvas:transparent');

		$cardStroke = new ImagickDraw();
		$cardStroke->setFillColor(new ImagickPixel($hexColour));

		$cardStroke->roundRectangle(
			self::SIG_MARGIN + (self::SIG_STROKE_WIDTH / 2),
			self::SIG_MARGIN + (self::SIG_STROKE_WIDTH / 2),
			($this->baseWidth + self::SIG_MARGIN - 1) - (self::SIG_STROKE_WIDTH / 2),
			($this->baseHeight + self::SIG_MARGIN - 1) - (self::SIG_STROKE_WIDTH / 2),
			self::SIG_OUTER_ROUNDING * 2,
			self::SIG_OUTER_ROUNDING * 2);

		$cardStrokeImage->drawImage($cardStroke);

		$roundImage = new Imagick();
		$roundImage->newPseudoImage($this->getCanvas()->getImageWidth(), $this->getCanvas()->getImageHeight(), 'canvas:transparent');

		$roundMask = new ImagickDraw();
		$roundMask->setFillColor(new ImagickPixel('black'));
		$roundMask->roundRectangle(
			self::SIG_MARGIN + self::SIG_STROKE_WIDTH * 2,
			self::SIG_MARGIN + self::SIG_STROKE_WIDTH * 2,
			($this->baseWidth + self::SIG_MARGIN) - self::SIG_STROKE_WIDTH * 2 - 1,
			($this->baseHeight + self::SIG_MARGIN) - self::SIG_STROKE_WIDTH * 2 - 1,
			self::SIG_ROUNDING,
			self::SIG_ROUNDING
		);

		$roundImage->drawImage($roundMask);

		$cardStrokeImage->compositeImage(
			$roundImage,
			Imagick::COMPOSITE_DSTOUT,
			0,
			0
		);

		$this->canvas->compositeImage($cardStrokeImage, \Imagick::COMPOSITE_DEFAULT, 0, 0);
	}

	public function generate($hexColour = "#bb1177") {
		/*
			The inner width and height of the signature card.
			This excludes the margins.
		*/
		$this->baseWidth = $this->template->calculateBaseWidth();
		$this->baseHeight = $this->template->calculateBaseHeight();

		$this->drawShadow();
		$this->drawBackground($hexColour);
		$this->drawPlainArea();
		if ($this->template->hasTriangleStrip())
			$this->drawTriangleStrip($hexColour);
		$this->drawFinalStroke($hexColour);

		$this->template->drawComponents();

		// Sets the headers and echoes the image
		$this->output();
	}
}