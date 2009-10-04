<?php
/**
 * Copyright (c) 2009 Till Klampaeckel
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
 * A response object to wrap data from scr.im's API.
 *
 * @category Web Services
 * @package  Scrim
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  Release: @package_version@
 * @link     http://github.com/till/Services_Scrim
 */
class Services_Scrim_Response
{
    /**
     * @var array $data
     */
    protected $data = array();

    public function __construct()
    {
    }

    /**
     * Displays the scr.im URL.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->data['url'];
    }

    /**
     * Set the email address which we want to create a scr.im for.
     *
     * @param string $email The email address to create a scr.im for.
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->data['email'] = $email;
        return $this;
    }

    public function setOld($flag)
    {
        if (!is_bool($flag)) {
            throw InvalidArgumentException('$flag must be a boolean.');
        }
        $this->data['old'] = $flag;
        return $this;
    }

    public function setResult($result)
    {
        $this->data['result'] = $result;
        return $this;
    }

    /**
     * Set the scr.im URL, e.g. http://scr.im/SCRIM
     *
     * @param string $scrim An optional scr.im in case you want a non-random one.
     *
     * @return $this
     * @throws InvalidArgumentException If the scr.im is too long.
     */
    public function setScrim($scrim)
    {
        $this->data['scrim'] = $scrim;
        return $this;
    }

    public function setUrl($url)
    {
        $this->data['url'] = $url;
        return $this;
    }

    /**
     * Return the email address.
     *
     * @return string
     * @uses   self::$data
     */
    public function getEmail()
    {
        return $this->data['email'];
    }

    public function getResult()
    {
        return $this->data['result'];
    }

    /**
     * Get the scr.im.
     *
     * @return string The scr.im.
     */
    public function getScrim()
    {
        return $this->data['scrim'];
    }

    /**
     * Get the URL.
     *
     * @return string The scr.im URL.
     * @throws RuntimeException When called before generate().
     */
    public function getUrl()
    {
        return $this->data['url'];
    }

    /**
     * Determine if the scr.im we generated is indeed new, or old.
     *
     * @return boolean
     */
    public function isOld()
    {
        return $this->data['old'];
    }
}