<?php
/**
 * A class to access the osu! API. It queries the API at {@link API_URL}.
 *
 * @author Lemmmy
 */
class OsuAPI
{
	/*
	 * The osu! API url
	 */
	const API_URL = "https://osu.ppy.sh/api/";

	/**
	 * Your private osu!API key
	 *
	 * @var string
	 */
	private $apiKey;

	/**
	 * The memcache object
	 *
	 * @var Memcached
	 */
	private $mc;

	/**
	 * Creates a new instance of OsuAPI
	 *
	 * @param string $apiKey Your private osu!API key
	 */
	public function __construct($apiKey)
	{
		$this->apiKey = $apiKey;

		$this->mc = Utils::getMemcache();
	}

	/**
	 * Gets user information for a specific game mode
	 *
	 * @param string $username The player's username
	 * @param string [$mode] The game mode
	 *
	 * @return array|bool
	 */
	public function getUserForMode($username, $mode = 0)
	{
		if ($mode < 4) {
			$user = $this->mc->get("osusigv3_user_" . $mode . "_" . strtolower($username));

			if (!$user) {
				$request = $this->request('get_user', ['u' => $username, 'm' => $mode]);

				if (isset($request) && isset($request[0])) {
					$this->mc->set("osusigv3_user_" . $mode . "_" . strtolower($username), $request[0], 180);

					return $request[0];
				}
			} else {
				return $user;
			}

			return false;
		} else {
			$errorImage = new ErrorImage();
			$errorImage->generate("Invalid mode", "You specified an invalid mode\nfor the signature.");

			return false;
		}

		return false;
	}

	/**
	 * Request from the osu!API
	 *
	 * @param string $url The resource to fetch
	 * @param array $params A list of arguments to give for the resource
	 *
	 * @return array|null The decoded JSON object containing the fetched resource, or null.
	 */
	public function request($url, $params = [])
	{
		$params = array_merge(["k" => $this->apiKey], $params);
		$url = static::API_URL . $url . '?' . http_build_query($params);

		return $this->decode(file_get_contents($url));
	}

	/**
	 * Decode a response from the API
	 *
	 * @param string $content The response from the API
	 * @return array|null The decoded JSON object, or null.
	 */
	protected function decode($content)
	{
		if (strlen($content) > 0 && $content) {
			return json_decode($content, true);
		}

		return null;
	}
}
