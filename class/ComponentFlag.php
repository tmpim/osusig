<?php
/**
 * @author Lemmy
 */
class ComponentFlag extends Component
{
	const FLAGS_DIRECTORY = 'flags/';

	/**
	 * The width of this flag
	 *
	 * @var int
	 */
	private $width;

	/**
	 * The height of this flag
	 *
	 * @var int
	 */
	private $height;


	/**
	 * The memcache object
	 *
	 * @var Memcached
	 */
	private $mc;

	public function __construct(OsuSignature $signature, $x = 0, $y = 0, $width = 18, $height = 12) {
		parent::__construct($signature, $x, $y);

		$this->width = $width;
		$this->height = $height;

		$this->mc = new Memcached();
		$this->mc->addServer("localhost", 11211);
	}

	public function getWidth() {
		return $this->width;
	}

	public function getHeight() {
		return $this->height;
	}

	/**
	 * Gets the user's flag
	 *
	 * @param $user array The user
	 *
	 * @return Imagick|null The flag, or nothing.
	 */
	private function getFlag($user) {
		$country = $user['country'];

		if (!$country) return null;

		$flag = new Imagick();
		$cachedPicture = $this->mc->get("osusigv3_flag_" . strtolower($country));

		if (!$cachedPicture) {
			$flagBlob = @file_get_contents(self::FLAGS_DIRECTORY . $country . '.png');

			if ($flagBlob === false) {
				return null;
			}

			$flag->readImageBlob($flagBlob);;
			$flag->setImageFormat('png');

			$this->mc->set("osusigv3_flag_" . strtolower($country), base64_encode($flag->getImageBlob()), 43200);

			return $flag;
		} else {
			$decodedPicture = base64_decode($cachedPicture);
			$flag->readImageBlob($decodedPicture);
			$flag->setImageFormat('png');

			return $flag;
		}
	}

	public function draw(OsuSignature $signature)
	{
		parent::draw($signature);

		$flag = $this->getFlag($signature->getUser());

		if ($flag) {
			$flag->resizeImage(
				$this->getWidth(),
				$this->getHeight(),
				Imagick::FILTER_CATROM,
				1
			);

			if (isset($_GET['flagshadow'])) {
				$shadow = $flag->clone();
				$shadow->setImageBackgroundColor(new ImagickPixel('black'));

				$shadow->shadowImage(50, 3, 0, 0);

				$signature->getCanvas()->compositeImage($shadow, \Imagick::COMPOSITE_DEFAULT, $this->x - 6, $this->y - 6);
			}

			if (isset($_GET['flagstroke'])) {
				$flagStroke = new \ImagickDraw();

				$flagStroke->setFillColor("#FFFFFFEE");
				$flagStroke->roundRectangle($this->x - 1, $this->y - 1, ($this->x - 1) + ($this->getWidth() + 1), ($this->y - 1) + ($this->getHeight() + 1), 1, 1);

				$signature->getCanvas()->drawImage($flagStroke);
			}

			$signature->getCanvas()->compositeImage(
				$flag,
				Imagick::COMPOSITE_DEFAULT,
				$this->x,
				$this->y
			);
		}
	}
}