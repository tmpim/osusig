<?php
/**
 * @author Lemmmy
 */
class TemplateNormal extends Template
{
	private static function getGDFontSize ($size) {
		return ($size * 3) / 4;
	}

	public function __construct(OsuSignature $signature) {
		parent::__construct($signature);

		$isCountryRank = isset($_GET['countryrank']);
		$removeAvatarMargin = isset($_GET['removeavmargin']);
		$showPP = isset($_GET['pp']);
		$pp = $showPP ? $_GET['pp'] : -1;

		if ($showPP && ($pp < 0 || $pp > 3)) {
			$errorImage = new ErrorImage();
			$errorImage->generate("Invalid parameter", "Parameter 'pp' has an\ninvalid value");
		}

		$userRank = $signature->getUser()['pp_rank'];
		$userRank = $userRank ? $userRank : '?';
		$userCountryRank = $signature->getUser()['pp_country_rank'];
		$userCountryRank = $userCountryRank ? $userCountryRank : '?';
		$mode = isset($_GET['mode']) ? $_GET['mode'] : 'osu';
		$iconmode = 0;
		switch ($mode) {
			case 0:
				$mode = 'osu';
				$iconmode = 0;
				break;
			case 1:
				$mode = 'taiko';
				$iconmode = 3;
				break;
			case 2:
				$mode = 'ctb';
				$iconmode = 2;
				break;
			case 3:
				$mode = 'mania';
				$iconmode = 1;
				break;
		}
		$username = $signature->getUser()['username'];
		$ppText = $signature->getUser()['pp_raw'];

		$avatar = new ComponentAvatar(
			$signature,
			$signature::SIG_MARGIN + ($removeAvatarMargin ? 4 : 6),
			$signature::SIG_MARGIN + ($removeAvatarMargin ? 4 : 6),
			$removeAvatarMargin ? 80 : 76,
			$removeAvatarMargin ? 80 : 76
		);

		$rank = new ComponentLabel(
			$signature,
			$isCountryRank ? 325 : 287,
			32,
			'#' . number_format($userRank) . ($isCountryRank ? " (" . '#' . number_format($userCountryRank) . ')' : ""),
			ComponentLabel::FONT_REGULAR,
			'#FFFFFF',
			$isCountryRank ? 12 : 14,
			\Imagick::ALIGN_RIGHT,
			0
		);

		$mode = new ComponentLabel(
			$signature,
			$isCountryRank ? 315 : 290,
			$isCountryRank ? 18 : 31,
			json_decode('"\\ue00' . $iconmode . '"'),
			ComponentLabel::FONT_OSU,
			'#FFFFFF',
			$isCountryRank ? 12 : 14,
			\Imagick::ALIGN_LEFT,
			$isCountryRank ? 12 : 14
		);

		$flag = new ComponentFlag(
			$signature,
			$isCountryRank ? 297 : 307,
			$isCountryRank ? 10 : 21,
			$isCountryRank ? 13 : 18,
			$isCountryRank ? 9 : 12
		);

		$nameFontSize = 24;
		$nameFont = ComponentLabel::FONT_DIRECTORY . ComponentLabel::FONT_MEDIUM;

		$nameDimensions = imagettfbbox(static::getGDFontSize($nameFontSize), 0, $nameFont, $username);
		$nameTextWidth = abs($nameDimensions[4] - $nameDimensions[0]);

		while ($nameTextWidth > ($isCountryRank ? 235 : 198) - $rank->getActualWidth()) {
			$nameDimensions = imagettfbbox(static::getGDFontSize($nameFontSize), 0, $nameFont, $username);
			$nameTextWidth = abs($nameDimensions[4] - $nameDimensions[0]);

			$nameFontSize -= 0.5;
		}

		$name = new ComponentLabel(
			$signature,
			90,
			32,
			$username,
			ComponentLabel::FONT_MEDIUM,
			'#FFFFFF',
			$nameFontSize,
			\Imagick::ALIGN_LEFT,
			-2
		);

		if ($showPP && $pp == 2) {
			$ppLabel = new ComponentLabel(
				$signature,
				$isCountryRank ? 293 : 326,
				18,
				number_format(floor($ppText)) . 'pp',
				ComponentLabel::FONT_REGULAR,
				'#FFFFFF',
				10,
				\Imagick::ALIGN_RIGHT,
				-2
			);

			$this->addComponent($ppLabel);
		}

		$accuracyLabel = new ComponentLabel(
			$signature,
			91,
			56,
			"Accuracy",
			ComponentLabel::FONT_REGULAR,
			'#555555',
			14,
			\Imagick::ALIGN_LEFT
		);

		$playCountLabel = new ComponentLabel(
			$signature,
			91,
			73,
			"Play Count",
			ComponentLabel::FONT_REGULAR,
			'#555555',
			14,
			\Imagick::ALIGN_LEFT
		);

		$this->addComponent($avatar);
		$this->addComponent($rank);
		$this->addComponent($mode);
		$this->addComponent($flag);
		$this->addComponent($name);
		$this->addComponent($accuracyLabel);
		$this->addComponent($playCountLabel);

		// I don't know either.
		$this->extraWidth = OsuSignature::SIG_MARGIN * 2 + 1 - ($isCountryRank ? 2 : 0);
		$this->extraHeight = $removeAvatarMargin ? 1 : 3;
	}
}
