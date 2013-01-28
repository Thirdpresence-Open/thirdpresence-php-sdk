thirdpresence-php-sdk
=====================

PHP SDK for the Thirdpresence platform API.

Thirdpresence HTTP API can be found from:
http://wiki.thirdpresence.com/index.php/API_Reference

You need the authentication token given by ThirdPresence when you
registered for the ThirdPresence service. The token is available
in your management console in the API tab. Log into the console from:
http://console.thirdpresence.com


Usage Examples
--------------

    require "src/thirdpresence.php";
    $tpr = new Thirdpresence(self::API_KEY);
    $reply = $tpr->getVideosByCategory(-1);


TODO: More examples will follow as the implementation continues


Tests
-----

NOTE: You can only run the tests by having a valid Thirdpresence API key
which you must set in place into tests/tests.php for running tests.

Run the Thirdpresence SDK unit tests using the following command:

    phpunit --stderr --bootstrap tests/bootstrap.php tests/tests.php
