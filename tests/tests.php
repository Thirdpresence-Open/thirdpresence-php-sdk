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
    const API_KEY = 'just_a_test_key';

    public function testConstructor() {
        $facebook = new Thirdpresence(self::API_KEY);
        $this->assertEquals($facebook->getApiKey(), self::API_KEY,
                'Expect the Api key to be set.');
    }

}

?>
