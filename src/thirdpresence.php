<?php
/**
 * Copyright 2013 ThirdPresence
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details:
 * 
 * <http://www.gnu.org/licenses/>.
 */

//TODO: This is just the implementation skeleton so far. More will follow.

class Thirdpresence {
    private $apikey = '';

    const API_BASE_URL = "http://api.thirdpresence.com/";
    const API_VERSION = "10-09";

    private static $ACTIONS = array(
        // ACTION => [HTTP METHOD, URL NAMESPACE, VERSION)
        "getVideos" => array("GET", "video", "10-09"),
        "getVideoById" => array("GET", "video", "10-09"),
        "getVideosByDesc" => array("GET", "video", "10-09"),
        "getVideosByCategory" => array("GET", "video", "10-09"),
        "getDeliveryStatus" => array("GET", "video", "10-09"),
        "insertVideo" => array("POST", "video", "10-09"),
        "deleteVideo" => array("GET", "video", "10-09"),
        "updateVideoData" => array("POST", "video", "10-09"),

        "listCategories" => array("GET", "category", "10-09"),
        "addVideoCategory" => array("GET", "category", "10-09"),
        "deleteCategory" => array("GET", "category", "10-09"),
        "updateCategory" => array("GET", "category", "10-09"),

        "addToken" => array("GET", "auth", "04-10"),
        "removeToken" => array("GET", "auth", "04-10"),

        "createNewAccount" => array("GET", "account", "05-10"),
        "getSubaccounts" => array("GET", "account", "05-10"),

        "stitchVideos" => array("POST", "ads", "06-11"),
    );

    /**
     * Initialize a Thirdpresence application.
     *
     * @param string $apikey The Thirdpresence SDK API key.
     */
    public function __construct($apikey) {
        $this->apikey = $apikey;
    }

    /**
     * Get the API key set in initialization.
     *
     * @return string The API key.
     */
    public function getApiKey() {
        return $this->apikey;
    }

}

?>
