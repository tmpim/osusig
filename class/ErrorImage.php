<?php
/**
 * Generates an error image to die with.
 *
 * @author Lemmmy
 */
class ErrorImage
{
	/**
	 * The canvas to draw to.
	 *
	 * @var Imagick
	 */
	private $canvas;

	/**
	 * Initializes an error image.
	 */
	public function __construct() {
		$this->canvas = new\Imagick();
	}

	/**
	 * Generates and dies with an image containing the heading and the text of the error.
	 *
	 * @param string $headingText The heading of the error.
	 * @param string $errorText The text of the error.
	 */
	public function generate($headingText, $errorText) {
		$draw = new ImagickDraw();

		$draw->setFillColor('#777777');
		$draw->setFontSize(15);

		$draw->setFont('fonts/exo2bold.ttf');
		$headingMetrics = $this->canvas->queryFontMetrics($draw, $headingText);

		$draw->setFont('fonts/exo2regular.ttf');
		$textMetrics = $this->canvas->queryFontMetrics($draw, $errorText);

		$this->canvas->newImage(
			max($textMetrics['textWidth'], $headingMetrics['textWidth']) + 6,
			$textMetrics['textHeight'] + $headingMetrics['textHeight'] + 6,
			new ImagickPixel('white'));

		$this->canvas->annotateImage($draw, 3, $headingMetrics['textHeight'] * 2, 0, $errorText);

		$draw->setFont('fonts/exo2bold.ttf');
		$draw->setFillColor('#333333');
		$draw->setGravity(Imagick::GRAVITY_NORTH);

		$this->canvas->annotateImage($draw, 3, 3, 0, $headingText);

		$this->canvas->setImageFormat('png');

		header('Content-Type: image/'.$this->canvas->getImageFormat());

		header("Cache-Control: max-age=60");
		header("Expires: ".gmdate("D, d M Y H:i:s", time()+60)." GMT");

		die($this->canvas);
	}
}