<?php
/**
 * The original osu!nextsig signature card. This is the design that followed through from v1 to v3. This is a pretty
 * messy class.
 *
 * @author Lemmmy
 */
class TemplateNormal extends Template
{
	private static function getGDFontSize ($size) {
		return ($size * 3) / 4;
	}

	public function __construct(OsuSignature $signature) {
		parent::__construct($signature);

		$user = $signature->getUser();

		$this->setCard(new CardRegular($user));

		$isCountryRank = isset($_GET['countryrank']);
		$removeAvatarMargin = isset($_GET['removeavmargin']);
		$showPP = isset($_GET['pp']);
		$pp = $showPP ? $_GET['pp'] : -1;
		$darkHeader = isset($_GET['darkheader']);
		$showRankedScore = isset($_GET['rankedscore']);
		$showXPBar = isset($_GET['xpbar']);

		if ($showPP && ($pp < 0 || $pp > 3)) {
			$errorImage = new ErrorImage();
			$errorImage->generate("Invalid parameter", "Parameter 'pp' has an\ninvalid value");
		}

		$userRank = $user['pp_rank'];
		$userRank = $userRank ? $userRank : '?';
		$userCountryRank = $user['pp_country_rank'];
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
		$username = $user['username'];
		$ppText = $user['pp_raw'];
		$accuracy = $user['accuracy'];
		$playcount = $user['playcount'];
		$level = $user['level'];

		$headerTextColour = $darkHeader ? '#555555' : '#FFFFFF';

		$avatar = new ComponentAvatar(
			$signature,
			CardRegular::SIG_MARGIN + ($removeAvatarMargin ? 4 : 6),
			CardRegular::SIG_MARGIN + ($removeAvatarMargin ? 4 : 6),
			$removeAvatarMargin ? 80 : 76,
			$removeAvatarMargin ? 80 : 76
		);

		$rank = new ComponentLabel(
			$signature,
			$isCountryRank ? 325 : 287,
			32,
			'#' . number_format($userRank) . ($isCountryRank ? " (" . '#' . number_format($userCountryRank) . ')' : ""),
			ComponentLabel::FONT_REGULAR,
			$headerTextColour,
			$isCountryRank ? 12 : 14,
			\Imagick::ALIGN_RIGHT,
			0
		);

		$mode = new ComponentLabel(
			$signature,
			$isCountryRank ? 313 : 290,
			$isCountryRank ? 18 : 31,
			json_decode('"\\ue00' . $iconmode . '"'),
			ComponentLabel::FONT_OSU,
			$headerTextColour,
			$isCountryRank ? 12 : 14,
			\Imagick::ALIGN_LEFT,
			$isCountryRank ? 12 : 14
		);

		$flag = new ComponentFlag(
			$signature,
			$isCountryRank ? 294 : 307,
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
			$headerTextColour,
			$nameFontSize,
			\Imagick::ALIGN_LEFT,
			-2
		);

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
			$showRankedScore ? "Ranked Score" : "Play Count",
			ComponentLabel::FONT_REGULAR,
			'#555555',
			14,
			\Imagick::ALIGN_LEFT
		);

		$accuracyText = round($accuracy, 2) . '%' . ($showPP && $pp == 1 ? ' (' . number_format(floor($ppText)) . 'pp)' : '');
		$accuracyValueLabel = new ComponentLabel(
			$signature,
			325,
			56,
			$accuracyText,
			ComponentLabel::FONT_BOLD,
			'#555555',
			14,
			\Imagick::ALIGN_RIGHT,
			-2
		);

		$playCountText = number_format($playcount) . ($showPP && $pp == 0 ? ' (' . number_format(floor($ppText)) . 'pp)' : ($showXPBar ? '' : ' (lv' . floor($level) . ')'));
		$playCountValueLabel = new ComponentLabel(
			$signature,
			325,
			73,
			$showRankedScore ? Utils::largeNumberFormat($user['ranked_score']) : $playCountText,
			ComponentLabel::FONT_BOLD,
			'#555555',
			14,
			\Imagick::ALIGN_RIGHT,
			-2
		);

		$this->addComponent($avatar);
		$this->addComponent($rank);
		$this->addComponent($mode);
		$this->addComponent($flag);
		$this->addComponent($name);
		$this->addComponent($accuracyLabel);
		$this->addComponent($playCountLabel);
		$this->addComponent($accuracyValueLabel);
		$this->addComponent($playCountValueLabel);

		if ($showPP && $pp == 2) {
			$ppLabel = new ComponentLabel(
				$signature,
				$isCountryRank ? 290 : 326,
				18,
				number_format(floor($ppText)) . 'pp',
				ComponentLabel::FONT_REGULAR,
				$headerTextColour,
				10,
				\Imagick::ALIGN_RIGHT,
				-2
			);

			$this->addComponent($ppLabel);
		}

		$onlineIndicator = isset($_GET['onlineindicator']) ? $_GET['onlineindicator'] : false;
		$online = $onlineIndicator == 2 || $onlineIndicator == 3 ? Utils::isUserOnline($user['username']) : false;

		if ($online) {
			$onlineIndicatorImage = new ComponentImage(
				$signature,
				$avatar->x + $avatar->getWidth() - 17,
				$avatar->y + $avatar->getHeight() - 17,
				ComponentImage::IMG_DIRECTORY . 'online_indicator.png'
			);

			$this->addComponent($onlineIndicatorImage);
		}

		if ($showXPBar) {
			$xpBar = new ComponentXPBar(
				$signature,
				92,
				79,
				isset($_GET['xpbarhex']) ? '' : '#ffa200',
				233,
				4
			);

			$this->addComponent($xpBar);
		}

		// I don't know either.
		$this->extraWidth = CardRegular::SIG_MARGIN * 2 + 1;
		$this->extraHeight = $removeAvatarMargin ? 1 : 3;
	}

	/**
	 * @return int The width to be added to the image.
	 */
	public function getImageMarginWidth()
	{
		return CardRegular::SIG_MARGIN * 2;
	}

	/**
	 * @return int The height to be added to the image.
	 */
	public function getImageMarginHeight()
	{
		return CardRegular::SIG_MARGIN * 2;
	}
}
