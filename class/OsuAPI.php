<?php
// Thanks to Cygnix
// Created by Lemmmy

class OsuAPI
{
    const API_URL = "https://osu.ppy.sh/api/";

    private $modes = ["osu", "taiko", "ctb", "mania"];
    private $apiKey;

    /**
     * Creates a new instance of OsuAPI
     *
     * @param string $apiKey [Your private osu!API key]
     */
    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * Gets user information for a specific game mode
     *
     * @param string $username [The player's username]
     * @param [string] $mode [The game mode]
     * $return array | false
     */
    public function getUserForMode($username, $mode = "osu") {
        if (in_array($mode, $this->modes)) {
            $query  = [
                "k" => $this->apiKey,
                "u" => $username,
                "m" => $mode,
            ];

            $request = file_get_contents(self::API_URL . "get_user?" . http_build_query($query));

            if ($request) {
                $apiResult = json_decode($request);

                if (isset($apiResult[0])) {
                    return $apiResult[0];
                }
            }

            return false;
        }
    }
}