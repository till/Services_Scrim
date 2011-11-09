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
 * HTTP_Request2
 * @ignore
 */
require_once 'HTTP/Request2.php';

/**
 * A wrapper around scr.im's API.
 *
 * @category Web Services
 * @package  Scrim
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  Release: @package_version@
 * @link     http://github.com/till/Services_Scrim
 */
class Services_Scrim
{
    /**
     * @var string $apiEndpoint
     */
    protected $apiEndpoint = 'http://scr.im/xml/';

    /**
     * @var array $data
     */
    protected $data = array();

    /**
     * @var HTTP_Request2 $httpRequest
     */
    protected $httpRequest;

    /**
     * Constructor. Inject a custom HTTP_Request2 object, if not, create one.
     *
     * @param HTTP_Request2 $httpRequest
     *
     * @return Services_Scrim
     */
    public function __construct(HTTP_Request2 $httpRequest = null)
    {
        if ($httpRequest !== null) {
            $this->httpRequest = $httpRequest;
        } else {
            $this->httpRequest = new HTTP_Request2;
        }
        $this->httpRequest->setMethod(HTTP_Request2::METHOD_POST);
        $this->httpRequest->setUrl($this->apiEndpoint);
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
        $scrim = trim($scrim);
        if (strlen($scrim) > 13) {
            // this value is not advertised, but the API chops off > 13
            throw new InvalidArgumentException("Your scr.im is too long.");
        }
        $this->data['scrim'] = $scrim;
        return $this;
    }

    /**
     * Generate a scr.im url. Make sure to call {@link self::setEmail()} and
     * optionally {@link self::setScrim()} before you call this.
     *
     * @return Services_Scrim_Response
     *
     * @uses   self::makeRequest()
     * @uses   self::parseResponse()
     */
    public function generate()
    {
        if (empty($this->data['scrim'])) {
            unset($this->data['scrim']);
        }

        $response = $this->makeRequest($this->data);
        $scrim    = $this->parseResponse($response);

        return $scrim;
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
     * Make the HTTP request against the scr.im API.
     *
     * @param $data array POST parameters.
     *
     * @return HTTP_Request2_Response
     * @uses   self::$httpRequest
     */
    protected function makeRequest(array $data)
    {
        $this->httpRequest->addPostParameter($data);
        $response = $this->httpRequest->send();

        return $response;
    }

    /**
     * Parse the response from scr.im. Throw exceptions if anything is not
     * according to what we expect.
     *
     * @param HTTP_Request2_Response $response The response object.
     *
     * @return Services_Scrim_Response
     *
     * @throws UnexpectedValueException In case scr.im returns anything but 200.
     * @throws UnexpectedValueException In case something else goes wrong.
     * @throws LogicException If we can't parse the XML.
     * @throws LogicException When scr.im returns valid XML, which contains an
     *         error message.
     */
    protected function parseResponse(HTTP_Request2_Response $response)
    {
        $body   = trim($response->getBody());
        $status = (int) $response->getStatus();

        // scr.im's a bit of a sucky api. It doesn't adhere to the usual REST/HTTP
        // standard and always returns 200, even on error. So in case it returns
        // something else, something must be wrong.
        if ($status !== 200) {
            throw new UnexpectedValueException('scr.im is currently down.');
        }

        // handle errors ourselves
        libxml_use_internal_errors(true);
        $obj = simplexml_load_string($body);

        if ($obj === false){
            $msg    = '';
            foreach (libxml_get_errors() as $error) {
                $msg .= "{$error->message}, ";
            }
            libxml_clear_errors();
            throw new LogicException("Could not parse scr.im's XML response: {$body}, error: {$msg}");
        }

        $result = (string) $obj->result;

        if ($result != 'Success') {

            $msg = 'This email is already stored in our database,';
            $len = strlen($msg); // FIXME: ;-)

            if ($msg != substr($result, 0, $len)) {
                throw new LogicException($obj->result);
            }

            // scrim is already in the database
            $old = true;
        } else {
            $old = false;
        }

        $email = (string) $obj->email;

        var_dump($obj);

        if ($email != $this->data['email']) {
            $msg = 'scr.im returned a url for the wrong email.';
            throw new UnexpectedValueException($msg);
        }

        $response = new Services_Scrim_Response;
        $response->setEmail($email)
            ->setOld($old)
            ->setResult($result)
            ->setScrim((string) $obj->scrim)
            ->setUrl((string) $obj->url);

        return $response;
    }

    /**
     * Reset all variables.
     *
     * @return void
     */
    public function reset()
    {
        $this->data = array();
    }

    /**
     * Autoloader
     *
     * @param string $className The class to load.
     *
     * @return boolean
     */
    public static function autoload($className)
    {
        if (substr($className, 0, 14) != 'Services_Scrim') {
            return false;
        }
        $file = dirname(__DIR__)
            . '/' . str_replace('_', '/', $className) . '.php';
        return require $file;
    }
}

spl_autoload_register(array('Services_Scrim', 'autoload'));
