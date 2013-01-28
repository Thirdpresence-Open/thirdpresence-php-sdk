thirdpresence-php-sdk
=====================

PHP SDK for the Thirdpresence platform API.

Thirdpresence HTTP API can be found from:
http://wiki.thirdpresence.com/index.php/API_Reference

You need the authentication token given by ThirdPresence when you
registered for the ThirdPresence service. The token is available
in your management console in the API tab. Log into the console from:
http://console.thirdpresence.com


Usage Example
-------------

First uou need to include the following line to start using this SDK:

    require "src/thirdpresence.php";

Following examples refer to the API token by variable: $apiKey

<b>Example 1</b>: Fetches all videos from a category and lists the id and name.

    $tpr = new Thirdpresence($apiKey);
    $videos = $tpr->getVideosByCategory($categoryId);
    foreach ($videos as $i => $vid) {
        echo $i . ":" . $vid['id'] . " Video '" . $vid['name'] . "'\n";
    }


Thirdpresence PHP SDK API
-------------------------

List all the videos for this account.<br/>
@param int $itemCount The amount of items to return. Give 0 to get all videos.

* `getVideos($itemCount)`

Gets the metadata of a video by the given (Thirdpresence) video id.<br/>
@param int $videoId The video ID given by the Thirdpresence platform.<br/>
@return array The video metadata as PHP array.

* `getVideoById($videoId)`

* `public function getVideoByReferenceId($referenceId)`
* `public function getVideosByDesc($text)`
* `public function getVideosByCategory($categoryId)`
* `public function insertVideo($videoMetadata)`
* `public function deleteVideo($videoId)`
* `public function deleteVideoByReferenceId($referenceId)`
* `public function updateVideoData($videoMetadata)`
* `public function getDeliveryStatus($videoId)`
* `public function getDeliveryStatusByReferenceId($referenceId)`
* `public function listCategories()`
* `public function addVideoCategory()`
* `public function deleteCategory()`
* `public function updateCategory()`
* `public function addToken()`
* `public function removeToken()`
* `public function createNewAccount()`
* `public function getSubaccounts()`
* `public function stitchVideos()`


Tests
-----

NOTE: You can only run the tests by having a valid Thirdpresence API key
which you must set in place into tests/tests.php for running tests.

Run the Thirdpresence SDK unit tests using the following command:

    phpunit --stderr --bootstrap tests/bootstrap.php tests/tests.php
