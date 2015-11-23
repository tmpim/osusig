<?php
/**
 * Small utility class.
 *
 * @author Lemmmy
 */
class Utils
{
	/**
	 * The useragent to use for cURL requests to the online indicator
	 */
	const USER_AGENT = "osu!next Signature Generator - lemmmy.pw/osusig/ - v3";

	/**
	 * The URL for the online indicator API thingy
	 */
	const ONLINE_URL = "http://onlineindicator.lemmmy.pw";

	/**
	 * The memcache singleton
	 *
	 * @var Memcached
	 */
	private static $mc;

	public static function getMemcache() {
		if (!isset(self::$mc)) {
			self::$mc = new Memcached();
			self::$mc->addServer("localhost", 11211);
		}

		return self::$mc;
	}

	public static function largeNumberFormat($number) {
		$n = round($number);

		$n_number_format = number_format($n);
		$n_array = explode(',', $n_number_format);
		$n_parts = array('k', 'M', 'B', 'T');
		$n_count_parts = count($n_array) - 1;

		$n_display = $n_array[0] . ((int) $n_array[1][0] !== 0 ? '.' . $n_array[1][0] : '');
		$n_display .= $n_parts[$n_count_parts - 1];

		return $n_display;
	}

	public static function isUserOnline($username) {
		$onlineStatus = self::$mc->get("osusig_v3_online_" . $username);

		if (!isset($onlineStatus) || empty($onlineStatus)) {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, self::ONLINE_URL . '?u=' . urlencode(str_replace(' ', '_', $username)));
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1250);

			$response = json_decode(curl_exec($ch));
			curl_close($ch);

			self::$mc->set("osusig_v3_online_" . $username, $response->online, 60);

			return $response->online;
		} else {
			return $onlineStatus;
		}
	}
}