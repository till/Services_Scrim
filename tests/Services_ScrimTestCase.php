<?php
/**
 * Copyright (c) 2009-2011 Till Klampaeckel
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * PHP Version 5
 *
 * @category Web Services
 * @package  Scrim
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: $Id$
 * @link     http://github.com/till/Services_Scrim
 */

/**
 * Services_Scrim
 */
require_once 'Services/Scrim.php';

/**
 * Services_ScrimTestCase
 *
 * @category Web Services
 * @package  Scrim
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  Release: @package_version@
 * @link     http://github.com/till/Services_Scrim
 */
class Services_ScrimTestCase extends PHPUnit_Framework_TestCase
{
    protected $obj;

    public function setUp()
    {
        $this->obj = new Services_Scrim;
    }

    public static function emailProvider()
    {
        return array(
            array(time() . '@example.org', false, null),
            array('foobar@example.org', true, 'foobarscrim'),
            array('foo@example.org', true, 'foo'),
        );
    }

    /**
     * Test scrim. ;-)
     *
     * @param string  $email The email address.
     * @param boolean $old   Is old, or is new scr.im.
     * @param mixed   $scrim A scrim URL, or null.
     *
     * @return void
     *
     * @dataProvider emailProvider
     */
    public function testScrim($email, $old, $scrim = null)
    {
        if ($scrim === null) {
            $scrim = 'ss' . time();
        }

        $this->obj->setScrim($scrim);
        $this->obj->setEmail($email);

        $response = $this->obj->generate();

        $url = 'http://scr.im/' . $scrim;

        $newScrim = $response->getScrim();
        $newUrl   = $response->getUrl();
        $isOld    = $response->isOld();

        $this->assertEquals($scrim, $newScrim);
        $this->assertEquals($old, $isOld);
        $this->assertEquals($url, $newUrl);
    }
}
