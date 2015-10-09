<?php
/**
 * @author Lemmy
 */
class OsuAPI
{
    const API_URL = "https://osu.ppy.sh/api/";
    /**
     * Each mode of osu! that we want to use
     * 
     * @var array
     */
    protected static $modes = ["osu", "taiko", "ctb", "mania"];
    
    /**
     * Your private osu!API key
     * 
     * @var string
     */
    private $apiKey;
    /**
     * Creates a new instance of OsuAPI
     *
     * @param string $apiKey Your private osu!API key
     */
    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }
    /**
     * Gets user information for a specific game mode
     *
     * @param string $username The player's username
     * @param string $mode The game mode
     * @return array|bool
     */
    public function getUserForMode($username, $mode = "osu") {
        if (in_array($mode, static::$modes)) {
            $request = $this->request('get_user', ['u' => $username, 'm' => $mode]);
            if ($request && isset($result[0])) {
                return $result[0];
            }
            return false;
        }
    }
    
    /**
     * Request from the osu!API
     * 
     * @param string $url The resource to fetch
     * @param array $params A list of arguments to give for the resource
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
        // todo: error handling here
        if (strlen($content) > 0 && $content) {
            return json_decode($content, true);
        }
        
        return null;
    }
}
