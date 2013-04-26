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
        "getDeliveryStatus" => array("GET", "video", "03-13"),
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

        "insertLinearVASTAd" => array("POST", "vast", "03-13"),
        "updateLinearVASTAd" => array("POST", "vast", "03-13"),
        "deleteLinearVASTAd" => array("GET", "vast", "03-13"),
        "getLinearVASTAdById" => array("GET", "vast", "03-13"),
        "getLinearVASTAds" => array("GET", "vast", "03-13"),
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
     * @return mixed Returns wither JSON data as array or string when applicable.
     */
    private function makeRequest($action, $paramsArray, $dataArray) {
        if (!array_key_exists($action, self::$ACTIONS)) {
            throw new Exception("Invalid action: " . $action);
        }
        $http_verb = self::$ACTIONS[$action][0];
        $url_namespace = self::$ACTIONS[$action][1];
        $version_str = self::$ACTIONS[$action][2];

        $reply_data = '';
        if ('GET' == $http_verb) {
            $reply_data = $this->makeGetRequest($action, $url_namespace,
                              $version_str, $paramsArray);
        }
        else if ('POST' == $http_verb) {
            $reply_data = $this->makePostRequest($action, $url_namespace,
                              $version_str, $paramsArray, $dataArray);
        }
        else {
            throw new Exception("Invalid HTTP verb: " . $http_verb);
        }

        //echo "REPLY: " . $reply_data . "\n";
        $json_data = json_decode($reply_data, TRUE);
        if (FALSE == $json_data) {
            if (NULL != $reply_data && strlen($reply_data) > 0) {
                $reply_data = trim($reply_data);
                if (strlen($reply_data) <= 30) {
                    return $reply_data;
                }
            }
            throw new Exception("Failed to decode reply: " . $reply_data);
        }
        else {
            return $json_data;
        }
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
        //echo "Making request to URL: " . $url . "\n";
        $reply_data = @file_get_contents($url, false, $headers);
        return $reply_data;
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
     * @param int $itemCount The amount of items to return. Give 0 to get all videos.
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
     * Gets a list of metadata if the given text appears in
     * the video description.
     * 
     * @param string $text The text to be searched from the description.
     * @return array The found video metadata as PHP array.
     */
    public function getVideosByDesc($text) {
        $params = array("text" => $text);
        return $this->makeRequest("getVideosByCategory", $params, NULL);
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

    /**
     * Deletes a video from the user's account by the given id.
     * 
     * @param int $videoId The video ID given by the Thirdpresence platform.
     * @return Ambigous <mixed, string>
     */
    public function deleteVideo($videoId) {
        $params = array("videoId" => $videoId);
        return $this->makeRequest("deleteVideo", $params, NULL);
    }

    /**
     * Deletes a video from the user's account by the reference id.
     * 
     * @param int $videoId The video ID given by the Thirdpresence platform.
     * @return Ambigous <mixed, string>
     */
    public function deleteVideoByReferenceId($referenceId) {
        $params = array("providerId" => $referenceId);
        return $this->makeRequest("deleteVideo", $params, NULL);
    }

    /**
     * Updates video metadata.
     * You must give at least the video 'id' and then any fields you want
     * to update in the metadata passed to this method.
     * 
     * Example metadata:
     * array(
     *  "name" => "New name for my video",
     *  "synopsis" => "A new synopsis for my video",
     *  "id" => 123456,
     * )
     * 
     * @param array $videoMetadata The video metadata for updating the video.
     * @return array The updated video metadata as PHP array.
     */
    public function updateVideoData($videoMetadata) {
        return $this->makeRequest("updateVideoData", NULL, $videoMetadata);
    }

    /**
     * Gets the status of a video by video id.
     * 
     * Returned status is one of the following values:
     *  ACTIVE
     *  PROCESSING
     *  INACTIVE
     *  ERROR
     *  REMOVED
     * 
     * @param int $videoId The video ID given by the Thirdpresence platform.
     * @return string Single string value as listed in the comment above.
     */
    public function getDeliveryStatus($videoId) {
        $params = array("videoId" => $videoId);
        return $this->makeRequest("getDeliveryStatus", $params, NULL);
    }

    /**
     * Gets the status of a video by customer given reference id.
     * 
     * Returned status is one of the following values:
     *  ACTIVE
     *  PROCESSING
     *  INACTIVE
     *  ERROR
     *  REMOVED
     * 
     * @param int $referenceId The video reference ID.
     * @return string Single string value as listed in the comment above.
     */
    public function getDeliveryStatusByReferenceId($referenceId) {
        $params = array("providerId" => $referenceId);
        return $this->makeRequest("getDeliveryStatus", $params, NULL);
    }

    /**
     * Lists the categories for the account.
     * 
     * Example of a returned category (one category inside a list):
     * array(
     *      "categoryId" => 12345,
     *      "name" => videos,
     *      "sourceurl" => "",
     *      "type" => "vodvideo",
     *      "iconlocation" => "http://thirdpresence-static-images.s3.amazonaws.com/default/iconClips.png"
     *  )
     * 
     * @return array The category metadata as PHP array.
     */
    public function listCategories() {
        return $this->makeRequest("listCategories", NULL, NULL);
    }

    /**
     * Adds a new video category with the given content.
     * 
     * If $sourceURL is given, it must point to a collection of videos, e.g.
     * in ThirdPresence's own FTP or customer's own RSS. See:
     * http://wiki.thirdpresence.com/index.php/Uploading_content_using_RSS
     * 
     * @param string $name The name of the new video category.
     * @param string $sourceURL The source URL for fetching content, or NULL.
     * @return array The added category metadata as PHP array.
     */
    public function addVideoCategory($name, $sourceURL) {
        $params = array("name" => $name);
        if (NULL != $sourceURL && strlen($sourceURL) > 0) {
            $params["sourceurl"] = $sourceURL;
        }
        return $this->makeRequest("addVideoCategory", $params, NULL);
    }

    /**
     * Deletes a video category with the given $categoryId.
     * 
     * If the $deleteContent is TRUE, then all the content in this
     * category will be deleted also. If delete_content is FALSE,
     * then all the content will be moved to the default category.
     * 
     * @param int $categoryId The ID of a video category.
     * @param bool $deleteContent True or False,
     *             i.e. whether to delete category contents.
     * @return string Simple message stating whether the content was deleted.
     */
    public function deleteCategory($categoryId, $deleteContent) {
        $params = array("categoryId" => $categoryId);
        if (TRUE == $deleteContent || (NULL != $categoryId && "true" == $categoryId) ) {
            $params["deleteContent"] = $deleteContent;
        }
        return $this->makeRequest("deleteCategory", $params, NULL);
    }

    /**
     * Updates a video category metadata.
     * You can update the category name or the source URL with this call.
     * 
     * If $sourceURL is given, it must point to a collection of videos, e.g.
     * in ThirdPresence's own FTP or customer's own RSS. See:
     * http://wiki.thirdpresence.com/index.php/Uploading_content_using_RSS
     * 
     * @param int $categoryId The numeric id of the category.
     * @param string $name The name of the new video category.
     * @param string $sourceURL The source URL for fetching content, or NULL.
     * @return array The category metadata as PHP array.
     */
    public function updateCategory($categoryId, $name, $sourceURL) {
        $params = array("categoryId" => $categoryId);
        if (NULL != $name && strlen($name) > 0) {
            $params["name"] = $name;
        }
        if (NULL != $sourceURL && strlen($sourceURL) > 0) {
            $params["sourceURL"] = $sourceURL;
        }
        return $this->makeRequest("updateCategory", $params, NULL);
    }

    /**
     * Adds an authorization token for a video.
     * 
     * @param string $videoId The ID of a video to add the token for.
     * @param string $contentAuthToken The authentication token.
     */
    public function addToken($videoId, $contentAuthToken) {
        $params = array("videoId" => $videoId,
                        "contentAAToken" => $contentAuthToken);
        return $this->makeRequest("addToken", $params, NULL);
    }

    /**
     * Removes an authorization token from a video.
     * 
     * @param string $videoId The ID of a video to remove the token from.
     * @param string $contentAuthToken The authentication token to be removed.
     */
    public function removeToken($videoId, $contentAuthToken) {
        $params = array("videoId" => $videoId,
                        "contentAAToken" => $contentAuthToken);
        return $this->makeRequest("removeToken", $params, NULL);
    }

    /**
     * Creates a new sub-account for a reseller account.
     * 
     * Created name for new account will be the reseller account prefixed
     * by the new given account name.
     * 
     * @param string $accountName The name for the new sub-account.
     * @param string $password Password for the new account console and statistics.
     * @param string $callback Callback URL to be called when a new updated video
     *                         for the account becomes available. Can be NULL.
     * @return array Newly created account metadata as PHP array.
     */
    public function createNewSubAccount($accountName, $password, $callback) {
        if (NULL == $accountName || strlen($accountName) < 3)
            throw new Exception("Invalid account name: " . $accountName);
        if (NULL == $password || strlen($password) < 6)
            throw new Exception("Invalid password: " . $password);
        $params = array("accountName" => $accountName,
                        "password" => $password);
        if (NULL != $callback && strlen($callback) > 0) {
            $params["callback"] = $callback;
        }
        return $this->makeRequest("createNewAccount", $params, NULL);
    }

    /**
     * List existing sub-accounts for a reseller account.
     * 
     * @return array A list of reseller sub-accounts as PHP array.
     */
    public function listSubaccounts() {
        return $this->makeRequest("getSubaccounts", NULL, NULL);
    }

    /**
     * Concatenates two videos based on the given metadata.
     * 
     * Mainly used for adding a preroll advertisement to a video.
     * Notice that the sourceurl and adurl point to video IDs
     * for videos already in the Thirdpresence platform.
     * 
     * Example metadata:
     * array(
     *     "name": "James Sanders provoca with preroll",
     *     "expiretime": "10.03.2012 02:17:08",
     *     "description": "Some description",
     *     "sourceurl": "300001",
     *     "adurl": "300002",
     *     "categoryid": 1179
     * )
     * 
     * @param array $metadata The video metadata. Example in the comment above.
     */
    public function stitchVideos($metadata) {
        return $this->makeRequest("stitchVideos", NULL, $metadata);
    }

    /**
     * Inserts a new video into user'a account that will be turned into
     * an advertisement that is available in VAST 3.0 format.
     * 
     * You can also use existing video items by providing key 'videoid'
     * with a id value pointing to some existing video item on your account.
     * 
     * See VAST specification at IAB net site: http://www.iab.net/vast
     * 
     * You can use following keywords (macros) that will be replaced if used in URLs:
     * 
     * [TIMESTAMP] will be replaced with the timestamp since epoch in seconds
     * [AD_ID] will be replaced by the VAST ad id 'adid'
     * [CREATIVE_ID] will be replaced by the creative id, but not if used in 'impressionurl'.
     * 
     * Example video metadata dict:
     * {
     *   "adid": "my_ad_id_0001",
     *   "description": "My first VAST advertisement",
     *   "impressionurl": "http://somehost/adserver/tracking/impressions/[AD_ID]?timestamp=[TIMESTAMP]",
     *   "trackingevents": {
     *     "resume": "http://somehost/adserver/tracking/events/resume/[CREATIVE_ID]?timestamp=[TIMESTAMP]",
     *     "start": "http://somehost/adserver/tracking/events/start/[CREATIVE_ID]?timestamp=[TIMESTAMP]",
     *     "complete": "http://somehost/adserver/tracking/events/complete/[CREATIVE_ID]?timestamp=[TIMESTAMP]"
     *   },
     *   "videoclicks": {
     *     "clickthrough": "http://somehost/adserver/tracking/clickthrough/[CREATIVE_ID]?timestamp=[TIMESTAMP]&link=http://mylandingsite/",
     *   },
     *   "releasetime": "2013-03-01T04:00:00Z",
     *   "expiretime": "2013-03-31T04:00:00Z",
     *   "sourceurl": "http://somehost/EXAMPLE.mp4",
     *   "categoryid": 1179
     * }
     * 
     * Notice that if you use 'videoid' key to use an existing video,
     * then the following keys will be ignored:
     * * releasetime
     * * expiretime
     * * sourceurl
     * * categoryid
     * 
     * @param array $metadata A dictionary with the video and ad metadata.
     * @return array The metadata of the added VAST ad in JSON format.
     */
    public function insertLinearVastAd($metadata) {
        return $this->makeRequest("insertLinearVASTAd", NULL, $metadata);
    }

    /**
     * Updates an existing VAST advertisement.
     * 
     * See example of the VAST ad object structure from method: insertLinearVastAd
     * 
     * @param array $metadata A dictionary with the video and ad metadata.
     * @return array The metadata of the updated VAST ad in JSON format.
     */
    public function updateLinearVastAd($metadata) {
        return $this->makeRequest("updateLinearVASTAd", NULL, $metadata);
    }

    /**
     * Deletes an existing VAST advertisement.
     * 
     * @param string $adid The Ad ID of the VAST ad to be deleted
     * @return array JSON object describing whether the delete succeeded.
     */
    public function deleteLinearVASTAd($adid) {
        $params = array("adid" => $adid);
        return $this->makeRequest("deleteLinearVASTAd", $params, NULL);
    }

    /**
     * Gets an existing VAST advertisement.
     * 
     * @param string $adid The Ad ID of the VAST ad to be retrieved
     * @return array The metadata of the retrieved VAST ad in JSON format.
     */
    public function getLinearVASTAdById($adid) {
        $params = array("adid" => $adid);
        return $this->makeRequest("getLinearVASTAdById", $params, NULL);
    }

    /**
     * Gets all the existing VAST advertisements.
     * 
     * @return array The metadata of the retrieved VAST ads in JSON format.
     */
    public function getLinearVASTAds($adid) {
        return $this->makeRequest("getLinearVASTAds", NULL, NULL);
    }

}

?>
