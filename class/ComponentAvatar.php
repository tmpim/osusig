<?php
/**
 * @author Lemmmy
 */
class ComponentAvatar extends Component
{
	const AVATAR_URL = 'https://a.ppy.sh/';

	/**
	 * The width of this avatar
	 *
	 * @var int
	 */
	private $width;

	/**
	 * The height of this avatar
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

	public function __construct(OsuSignature $signature, $x = 0, $y = 0, $width = 78, $height = 78) {
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
	 * Gets the user's avatar
	 *
	 * @param $user array The user
	 *
	 * @return Imagick|null The avatar, or nothing.
	 */
	private function getAvatar($user) {
		$avatarURL = self::AVATAR_URL . $user['user_id'] . '?' . time() . '.png';

		$avatar = new Imagick();
		$cachedPicture = $this->mc->get("osusigv3_avatar_" . strtolower($user['user_id']));

		if (!$cachedPicture) {
			$avatarBlob = @file_get_contents($avatarURL);

			if ($avatarBlob === false) {
				return null;
			}

			$matches = array();
			preg_match('#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $matches);

			if ($matches[1] == 200) {
				$avatar->readImageBlob($avatarBlob);;
				$avatar->setImageFormat('png');

				$this->mc->set("osusigv3_avatar_" . strtolower($user['user_id']), base64_encode($avatar->getImageBlob()), 43200);

				return $avatar;
			} else {
				return null;
			}
		} else {
			$decodedPicture = base64_decode($cachedPicture);
			$avatar->readImageBlob($decodedPicture);
			$avatar->setImageFormat('png');

			return $avatar;
		}
	}

	public function draw(OsuSignature $signature)
	{
		parent::draw($signature);

		$avatar = $this->getAvatar($signature->getUser());

		if ($avatar) {
			$avatar->resizeImage(
				$this->getWidth(),
				$this->getHeight(),
				Imagick::FILTER_CATROM,
				1
			);

			$roundImage = new Imagick();
			$roundImage->newPseudoImage($this->getWidth(), $this->getHeight(), 'canvas:transparent');

			$roundMask = new ImagickDraw();
			$roundMask->setFillColor(new ImagickPixel('black'));
			$roundMask->roundRectangle(
				0,
				0,
				$this->getWidth(),
				$this->getHeight(),
				OsuSignature::SIG_ROUNDING,
				OsuSignature::SIG_ROUNDING
			);

			$roundImage->drawImage($roundMask);

			$avatar->compositeImage(
				$roundImage,
				Imagick::COMPOSITE_DSTIN,
				0,
				0
			);

			$signature->getCanvas()->compositeImage(
				$avatar,
				Imagick::COMPOSITE_DEFAULT,
				$this->x,
				$this->y
			);
		}
	}
}