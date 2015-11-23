<?php
/**
 * The avatar component. This fetches the avatar from {@link AVATAR_URL} using the user's UID.
 *
 * If the user does not have an avatar, it will default to a blank #f8f8f8 square.
 *
 * `&opaqueavatar` can be globally set to draw a white background behind the avatar if the avatar is transparent.
 * `&avatarrounding` can be globally set to override the rounding set by the template.
 *
 * @author Lemmmy
 * @see Component
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
	 * The rounding of this avatar
	 *
	 * @var int
	 */
	private $rounding;

	/**
	 * The memcache object
	 *
	 * @var Memcached
	 */
	private $mc;

	/**
	 * @param OsuSignature $signature The base signature
	 * @param int $x The X position of this avatar
	 * @param int $y The Y position of this avatar
	 * @param int $width The width of this avatar
	 * @param int $height The height of this avatar
	 * @param int $rounding The rounding of this avatar (can be overriden by the user with `&avatarrounding`)
	 */
	public function __construct(
		OsuSignature $signature,
		$x = 0,
		$y = 0,
		$width = 78,
		$height = 78,
		$rounding = CardRegular::SIG_ROUNDING) {

		parent::__construct($signature, $x, $y);

		$this->width = $width;
		$this->height = $height;

		$this->rounding = $rounding;

		$this->mc = Utils::getMemcache();
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
		$avatarURL = self::AVATAR_URL . urlencode($user['user_id']) . '_' . urlencode(time()) . '.png';

		$avatar = new Imagick();
		$cachedPicture = $this->mc->get("osusigv3_avatar_" . $user['user_id']);

		if (!isset($cachedPicture) || !$cachedPicture) {
			$avatarBlob = @file_get_contents($avatarURL);

			if ($avatarBlob === false) {
				$avatar->newImage(128, 128, new ImagickPixel("#f8f8f8"));
				$avatar->setImageFormat('png');
			}

			$matches = array();
			preg_match('#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $matches);

			if ($matches[1] == 200) {
				$avatar->readImageBlob($avatarBlob);
				$avatar->setImageFormat('png');

				if (isset($_GET['opaqueavatar'])) {
					$avatarTemp = new Imagick();
					$avatarTemp->newImage($avatar->getImageWidth(), $avatar->getImageHeight(), new ImagickPixel('#ffffff'));
					$avatarTemp->setImageFormat('png');

					$avatarTemp->compositeImage($avatar, Imagick::COMPOSITE_DEFAULT, 0, 0);
					$avatar = $avatarTemp;
				}
			} else {
				$avatar->newImage(128, 128, new ImagickPixel("#f8f8f8"));
				$avatar->setImageFormat('png');
			}

			$this->mc->set("osusigv3_avatar_" . $user['user_id'], base64_encode($avatar->getImageBlob()), 43200);

			return $avatar;
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
			$fitByWidth = (($this->getWidth()/$avatar->getImageWidth())<($this->getHeight()/$avatar->getImageHeight())) ?true:false;

			if($fitByWidth){
				$avatar->thumbnailImage($this->getWidth(), 0, false);
			}else{
				$avatar->thumbnailImage(0, $this->getHeight(), false);
			}

			$avatarXOffset = ($this->getWidth() - $avatar->getImageWidth()) / 2;
			$avatarYOffset = ($this->getHeight() - $avatar->getImageHeight()) / 2;

			/*$avatar->resizeImage(
				$this->getWidth(),
				$this->get Height(),
				1
				Imagick::FILTER_CATROM
			);*/

			$avatarRounding = isset($_GET['avatarrounding']) ? max((int)$_GET['avatarrounding'], 0) : $this->rounding;

			$avatar->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);

			$roundImage = new Imagick();
			$roundImage->newPseudoImage($this->getWidth(), $this->getHeight(), 'canvas:transparent');

			$roundMask = new ImagickDraw();
			$roundMask->setFillColor(new ImagickPixel('black'));
			$roundMask->roundRectangle(
				0,
				0,
				$avatar->getImageWidth() - 1,
				$avatar->getImageHeight() - 1,
				$avatarRounding,
				$avatarRounding
			);

			$roundImage->drawImage($roundMask);
			$roundImage->setImageFormat('png');

			$avatar->compositeImage(
				$roundImage,
				Imagick::COMPOSITE_DSTIN,
				0,
				0,
				Imagick::CHANNEL_ALPHA
			);

			$signature->getCanvas()->compositeImage(
				$avatar,
				Imagick::COMPOSITE_DEFAULT,
				$this->x + $avatarXOffset,
				$this->y + $avatarYOffset
			);
		}
	}
}