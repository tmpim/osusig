<?php
/**
 * @author Lemmmy
 */
class TemplateNormal extends Template
{
	public function __construct(OsuSignature $signature) {
		parent::__construct($signature);

		$isCountryRank = isset($_GET['countryrank']);
		$removeAvatarMargin = isset($_GET['removeavmargin']);

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
			$isCountryRank ? 34 : 32,
			'#' . number_format($userRank) . ($isCountryRank ? " (" . '#' . number_format($userCountryRank) . ')' : ""),
			ComponentLabel::FONT_REGULAR,
			'#FFFFFF',
			$isCountryRank ? 12 : 14,
			\Imagick::ALIGN_RIGHT,
			1
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

		$this->addComponent($avatar);
		$this->addComponent($rank);
		$this->addComponent($mode);
		$this->addComponent($flag);

		// I don't know either.
		$this->extraWidth = OsuSignature::SIG_MARGIN * 2 + 1;
		$this->extraHeight = $removeAvatarMargin ? 1 : 3;
	}
}