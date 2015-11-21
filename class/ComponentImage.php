<?php
/**
 * Simple image component.
 *
 * @author Lemmmy
 * @see Component
 */
class ComponentImage extends Component
{
	const IMG_DIRECTORY = 'img/';

	/**
	 * The width of this image
	 *
	 * @var int
	 */
	private $width;

	/**
	 * The height of this image
	 *
	 * @var int
	 */
	private $height;

	/**
	 * The Imagick object of this image
	 *
	 * @var Imagick
	 */
	private $image;

	/**
	 * The composite to use for this image
	 *
	 * @var int
	 */
	private $composite;

	/**
	 * The filter to use for this image
	 *
	 * @var int
	 */
	private $filter;

	/**
	 * @param OsuSignature $signature The base signature
	 * @param int $x The X position of this image
	 * @param int $y The Y position of this image
	 * @param string|Imagick $image The actual image location
	 * @param int $composite The composite mode to use for this image
	 * @param int $width The width of this image. -1 = default to image size
	 * @param int $height The height of this image -1 = default to image size
	 * @param int $filter The Imagick filter to use for the scaling
	 */
	public function __construct(
		OsuSignature $signature,
		$x = 0,
		$y = 0,
		$image,
		$composite = Imagick::COMPOSITE_DEFAULT,
		$width = -1,
		$height = -1,
		$filter = Imagick::FILTER_CATROM) {

		parent::__construct($signature, $x, $y);

		if ($image instanceof Imagick) {
			$this->image = $image;
		} else {
			$this->image = new Imagick($image);
		}

		$this->width = $width;
		$this->height = $height;

		$this->composite = $composite;
		$this->filter = $filter;
	}

	public function getWidth() {
		return $this->width;
	}

	public function getHeight() {
		return $this->height;
	}

	public function draw(OsuSignature $signature)
	{
		parent::draw($signature);

		if ($this->width != -1 || $this->height != -1) {
			$this->image->resizeImage(
				$this->width == -1 ? $this->image->getImageWidth() : $this->width,
				$this->height == -1 ? $this->image->getImageHeight() : $this->height,
				1,
				$this->filter
			);
		}

		$signature->getCanvas()->compositeImage(
			$this->image,
			$this->composite,
			$this->x,
			$this->y
		);
	}
}