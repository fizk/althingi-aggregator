<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 29/03/2016
 * Time: 10:26 AM
 */

namespace AlthingiAggregator\Lib\Http\Client\Adapter;

use Zend\Http\Client\Adapter\AdapterInterface;
use Zend\Uri\Uri;

class LocalXmlAdapter implements AdapterInterface
{
    /** @var array */
    private $options = [];

    /** @var string */
    private $response;

    /**
     * @param string        $method
     * @param \Zend\Uri\Uri $uri
     * @param string        $httpVer
     * @param array         $headers
     * @param string        $body
     * @throws \Zend\Http\Client\Adapter\Exception\RuntimeException
     * @return string Request as string
     */
    public function write($method, $uri, $httpVer = '1.1', $headers = [], $body = '')
    {
        switch ($method) {
            case 'GET':
                $this->response = $this->processGet($uri);
                break;
            case 'DELETE':
                $this->response = $this->processRemove($uri);
                break;
            case 'POST':
            case 'PUT':
            case 'PATCH':
                $this->response = $this->processSave($method, $uri, $httpVer, $headers, $body);
                break;
            default:
                $this->response = $this->processUnsupported($method, $uri, $httpVer, $headers, $body);
                break;
        }

        return $this->response;
    }

    /**
     * Read response from server
     *
     * @throws \Zend\Http\Client\Adapter\Exception\RuntimeException
     * @return string
     */
    public function read()
    {
        return $this->response;
    }

    /**
     * Set the configuration array for the adapter
     *
     * @param array $options
     * @return $this
     */
    public function setOptions($options = [])
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Connect to the remote server
     *
     * @param string $host
     * @param int $port
     * @param  bool $secure
     */
    public function connect($host, $port = 80, $secure = false)
    {

    }

    /**
     * Close the connection to the server
     *
     */
    public function close()
    {

    }

    private function processGet(Uri $uri)
    {
        $response = '';

        $host = $uri->getHost();
        $protocol = $this->options['protocol'] ? : './';
        $path = $uri->getPath();
        $query = $uri->getQuery()
            ? '/?' . $uri->getQuery()
            : '';
        $ext = '.xml';
        $url = sprintf('%s%s%s%s%s', $protocol, $host, $path, $query, $ext);

        if (($content = @file_get_contents($url)) !== false) {
            $response .= "HTTP/1.1 200 OK\r\n";
            $response .= "Content-Type: text/xml; charset=UTF-8\r\n";
            $response .= "Content-Encoding: UTF-8\r\n";
            $response .= sprintf("Content-Length: %s\r\n", strlen($content));
            $response .= "Accept-Ranges: bytes\r\n";
            $response .= "Connection: close\r\n\r\n";

            $response .= $content;
        } else {
            $response .= "HTTP/1.1 404 Not Found\r\n";
            $response .= "Content-Type: text/xml; charset=UTF-8\r\n";
            $response .= "Content-Encoding: UTF-8\r\n";
            $response .= ("Content-Length: 0\r\n");
            $response .= "Accept-Ranges: bytes\r\n";
            $response .= "Connection: close\r\n\r\n";

            $response .= '.';
        }

        return $response;
    }

    private function processSave($method, Uri $uri, $httpVer, $headers, $body)
    {
        $host = $uri->getHost();
        $protocol = $this->options['protocol'] ? : './';
        $path = $uri->getPath();
        $query = $uri->getQuery()
            ? '/?' . $uri->getQuery()
            : '';
        $ext = '.xml';
        $url = sprintf('%s%s%s%s%s', $protocol, $host, $path, $query, $ext);
        $response = '';

        switch ($method) {
            case 'post':
                if (is_file($url)) {
                    $response = 'HTTP/1.1 400 Bad Request';
                    break;
                }
                // 201
                // HTTP/1.1 201 Created
                break;
            case 'push':
                if (is_file($url)) {
                    //FILE EXISTS ERROR
                    // 400
                    // HTTP/1.1 400 Bad Request
                }
                // 201
                // HTTP/1.1 201 Created
                break;
            case 'patch':
                if (!is_file($url)) {
                    //FILE DOES NOT EXISTS ERROR
                    // HTTP/1.1 404 Not Found
                }
                // 204
                // HTTP/1.1 204 No Content
                break;
        }
        $fileSuccess = file_put_contents($url, $body);
        $i = 0;

        return $response;

    }

    private function processRemove($uri)
    {

    }

    private function processUnsupported($method, $uri, $httpVer, $headers, $body)
    {

    }
}
