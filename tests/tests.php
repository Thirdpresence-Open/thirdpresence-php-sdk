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

class ThirdpresenceSDKTestCase extends PHPUnit_Framework_TestCase {
    const API_KEY = 'give_a_valid_test_key_here';

    public function testConstructor() {
        $tpr = new Thirdpresence(self::API_KEY);
        $this->assertEquals($tpr->getApiKey(), self::API_KEY,
                'Expect the Api key to be set.');
    }

//     public function testInsertVideo() {
//         $tpr = new Thirdpresence(self::API_KEY);
//         $x = array(
//             "name" => "TestVideo1",
//             "synopsis" => "Test synopsis",
//             "description" => "Test description",
//             "expiretime" => "10.03.2013 02:17:08",
//             "sourceurl" => "<valid_url_for_a_video>",
//         );
//         $reply = $tpr->insertVideo($x);
//         echo "\n- - - - -\n";
//         print_r($reply);
//     }

//     public function testGeneric() {
//         $tpr = new Thirdpresence(self::API_KEY);
//         $reply = $tpr->getVideosByCategory(-1);
//         echo "\n- - - - -\n";
//         print_r($reply);
//     }

}

?>
