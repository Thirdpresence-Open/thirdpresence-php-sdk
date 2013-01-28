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

class Thirdpresence {
    private $apikey = '';

    const USERAGENT = 'thirdpresence-php-1.0';
    const API_BASE_URL = "http://api.thirdpresence.com/";

    /**
     * Thirdpresence API available actions with matching HTTP method,
     * URL namespace and API version.
     * 
     * @var array
     */
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

    /**
     * Makes a generic Thirdpresence API request.
     * 
     * @param string $action The action matching Thirdpresence API actions.
     * @param array $paramsArray Array of request parameters to add to the standard request.
     * @param array $dataArray Array of data to be added as JSON to POST request.
     * @throws Exception In case the returned data cannot be encoded as JSON data.
     * @return array The response JSON data as PHP array.
     */
    private function makeRequest($action, $paramsArray, $dataArray) {
        if (!array_key_exists($action, self::$ACTIONS)) {
            throw new Exception("Invalid action: " . $action);
        }
        $http_verb = self::$ACTIONS[$action][0];
        $url_namespace = self::$ACTIONS[$action][1];
        $version_str = self::$ACTIONS[$action][2];

        if ('GET' == $http_verb) {
            return $this->makeGetRequest($action, $url_namespace, $version_str,
                       $paramsArray);
        }
        else if ('POST' == $http_verb) {
            return $this->makePostRequest($action, $url_namespace, $version_str,
                       $paramsArray, $dataArray);
        }
        throw new Exception("Invalid HTTP verb: " . $http_verb);
    }

    /**
     * Creates a URL pointing to the wanted resource in Thirdpresence platform API.
     * 
     * @param string $action The action matching Thirdpresence API actions.
     * @param string $url_namespace The URL namespace for the given action.
     * @param string $version_str The API version for this action URL.
     * @param array $paramsArray PHP array of paramteres to add to the URL as request parameters.
     * @return string The created URL.
     */
    private function getURL($action, $url_namespace, $version_str,
            $paramsArray) {
        $url = self::API_BASE_URL . $version_str . "/" . $url_namespace .
                   "/?authToken=" . $this->apikey . "&Action=" . $action .
                   "&version=" . $version_str;
        if (NULL != $paramsArray) {
            foreach ($paramsArray as $key => $value) {
                $url .= "&" . $key . "=" . $value;
            }
        }
        return $url;
    }

    /**
     * Makes a generic GET request to Thirdpresence API.
     */
    private function makeGetRequest($action, $url_namespace, $version_str,
            $paramsArray) {

        $url = $this->getURL($action, $url_namespace, $version_str, $paramsArray);
        $headerdata = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'Accept: application/json\r\n' .
                            'User-Agent: ' . self::USERAGENT . '\r\n'
            )
        );
        $headers = stream_context_create($headerdata);
        echo "URL: " . $url . "\n";
        $reply_data = @file_get_contents($url, false, $headers);
        echo "REPLY: " . $reply_data . "\n";
        $json_data = json_decode($reply_data, TRUE);
        if (FALSE == $json_data) {
            throw new Exception("Failed to decode JSON: " . $reply_data);
        }
        return $json_data;
    }

    /**
     * Makes a generic POST request to Thirdpresence API.
     */
    private function makePostRequest($action, $url_namespace, $version_str,
            $paramsArray, $dataArray) {

        $data_string = json_encode($dataArray);

        $url = $this->getURL($action, $url_namespace, $version_str, $paramsArray);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Accept: application/json',
                'User-Agent: ' . self::USERAGENT,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
            )
        );
        $result = curl_exec($ch);
        return $result;
    }

    /**
     * List all the videos for this account.
     *
     * @param int $itemCount The amount of items to return.
     */
    public function getVideos($itemCount) {
        $params = array();
        if ($itemCount != NULL && $itemCount > 0) {
            $params['itemCount'] = $itemCount;
        }
        return $this->makeRequest("getVideos", $params, NULL);
    }

    /**
     * Gets the metadata of a video by the given (Thirdpresence) video id.
     *
     * @param int $videoId The video ID given by the Thirdpresence platform.
     * @return array The video metadata as PHP array.
     */
    public function getVideoById($videoId) {
        $params = array("videoId" => $videoId);
        return $this->makeRequest("getVideoById", $params, NULL);
    }

    /**
     * Gets the metadata of a video by the given reference video id.
     *
     * @param int $referenceId The video reference ID given by the Thirdpresence platform user.
     * @return array The video metadata as PHP array.
     */
    public function getVideoByReferenceId($referenceId) {
        $params = array("providerId" => $referenceId);
        return $this->makeRequest("getVideoById", $params, NULL);
    }

    /**
     * Gets a list of metadata for all videos in given category.
     *
     * @param int $categoryId The ID of a video category.
     * @return array The video metadata for the given category as PHP array.
     */
    public function getVideosByCategory($categoryId) {
        $params = array("categoryId" => $categoryId);
        return $this->makeRequest("getVideosByCategory", $params, NULL);
    }

    /**
     * Inserts a video into the user's account.
     * You must pass the video metadata as a dictionary and it will
     * be encoded as JSON payload into the HTTP request.
     *
     * Example metadata:
     * array(
     *  "name" => "James Sanders provoca",
     *  "synopsis" => "A test video",
     *  "expiretime" => "10.03.2013 02:17:08",
     *  "description" => "Some description",
     *  "sourceurl" => "http://somehost/EXAMPLE.mp4",
     *  "categoryid" => 1179,
     * )
     *
     * @param array $videoMetadata Video metadata as a PHP array.
     * @return array The inserted video metadata as PHP array.
     */
    public function insertVideo($videoMetadata) {
        return $this->makeRequest("insertVideo", NULL, $videoMetadata);
    }

}

?>
