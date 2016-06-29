<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 5/06/2016
 * Time: 10:43 AM
 */

namespace AlthingiAggregator\Lib\Consumer;

include_once __DIR__ . '/Stub/ExtractorValidPost.php';
include_once __DIR__ . '/Stub/ExtractorValidPut.php';

use AlthingiAggregator\Lib\Consumer\Stub\ExtractorValidPut;
use PHPUnit_Framework_TestCase;
use Mockery;
use AlthingiAggregator\Lib\Consumer\Stub\ExtractorValidPost;
use Zend\Http\Client\Adapter\Test as ClientTestAdapter;
use Zend\Http\Client as ClientTest;
use Zend\Uri\Http;

class HttpConsumerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @tag 001
     */
    public function testNoIdentityNotInCacheUndefinedError()
    {
        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 500 Internal Server Error'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );

        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('critical')
            ->andReturnUsing(function ($code, $params) {
                $this->assertEquals(500, $code);
                $this->assertEquals('POST', $params[0]);
                return null;
            })
            ->getMock();
        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $response = $consumer->save(new \DOMElement('element'), 'this/key', new ExtractorValidPost());

        $this->assertInternalType('array', $response);
    }

    /**
     * @tag 002
     */
    public function testNoIdentityNotInCacheClientError()
    {
        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 400 Bad Request'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );

        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('error')
            ->andReturnUsing(function ($code, $params) {
                $this->assertEquals(400, $code);
                $this->assertEquals('POST', $params[0]);
                return null;
            })
            ->getMock();
        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $response = $consumer->save(new \DOMElement('element'), 'this/key', new ExtractorValidPost());

        $this->assertInternalType('array', $response);
    }

    /**
     * @tag 003
     */
    public function testNoIdentityNotInCachePostSuccess()
    {
        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 201 Created'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );

        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('debug')
            ->andReturnUsing(function ($code, $params) {
                $this->assertEquals(201, $code);
                $this->assertEquals('POST', $params[0]);
                return null;
            })
            ->once()
            ->getMock()
            ->shouldReceive('info')
            ->andReturn(null)
            ->once()
            ->getMock();
        
        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock()
            ->shouldReceive('setItem')
            ->andReturn(true)
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $response = $consumer->save(new \DOMElement('element'), 'this/key', new ExtractorValidPost());

        $this->assertInternalType('array', $response);
    }

    /**
     * @tag 004
     */
    public function testNoIdentityInCache()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $client = Mockery::mock('Zend\Http\Client')
            ->shouldReceive('send')
            ->andReturnNull()
            ->times(0)
            ->getMock();

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('info')
            ->andReturnUsing(function ($message) {
                $this->assertEquals('Already in cache', $message);
            })
            ->once()
            ->getMock();

        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn(HttpConsumer::createStorageValue($data))
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $consumer->save(new \DOMElement('element'), '', new ExtractorValidPost($data));
    }

    /**
     * @tag 005
     */
    public function testNoIdentityNotInCachePostConflictNoLocationHeader()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock();

        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );
        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('warning')
            ->andReturnUsing(function ($message, $params) {
                $this->assertEquals("Can't PATCH, no Location-Header", $message);
                return null;
            })
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $consumer->save(new \DOMElement('element'), '', new ExtractorValidPost($data));
    }

    /**
     * @tag 006
     */
    public function testNoIdentityNotInCachePostConflictHasLocationHeaderUndefinedError()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock();

        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Location: http://localhost:8080/path/to/resource'   . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );
        $adapter->addResponse(
            'HTTP/1.1 500 Internal Server Error'   . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );
        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('critical')
            ->andReturnUsing(function ($code, $params) {
                $this->assertEquals(500, $code);
                $this->assertEquals('PATCH', $params[0]);
                return null;
            })
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $consumer->save(new \DOMElement('element'), '', new ExtractorValidPost($data));
    }

    /**
     * @tag 007
     */
    public function testNoIdentityNotInCachePostConflictHasLocationHeaderSuccess()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock()
            ->shouldReceive('setItem')
            ->andReturn(true)
            ->once()
            ->getMock();

        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Location: http://localhost:8080/path/to/resource'   . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );
        $adapter->addResponse(
            'HTTP/1.1 204 No Content'   . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );
        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('info')
            ->andReturnUsing(function ($message, $params) {
                $this->assertEquals('Store in cache', $message);
                $this->assertEquals('PATCH', $params[0]);
                return null;
            })
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $consumer->save(new \DOMElement('element'), '', new ExtractorValidPost($data));
    }

    /**
     * @tag 008
     */
    public function testNoIdentityNotInCachePostConflictHasLocationHeaderResourceNotFound()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock();

        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Location: http://localhost:8080/path/to/resource'   . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );
        $adapter->addResponse(
            'HTTP/1.1 404 Not Found'   . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );
        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('error')
            ->andReturnUsing(function ($code, $params) {
                $this->assertEquals(404, $code);
                $this->assertEquals('PATCH', $params[0]);
                return null;
            })
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $consumer->save(new \DOMElement('element'), '', new ExtractorValidPost($data));
    }

    /**
     * @tag 101
     */
    public function testIdentityInCache()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $client = Mockery::mock('Zend\Http\Client')
            ->shouldReceive('send')
            ->andReturnNull()
            ->times(0)
            ->getMock();

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('info')
            ->andReturnUsing(function ($message) {
                $this->assertEquals('Already in cache', $message);
            })
            ->once()
            ->getMock();

        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn(HttpConsumer::createStorageValue($data))
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $consumer->save(new \DOMElement('element'), '', new ExtractorValidPut($data));
    }

    /**
     * @tag 102
     */
    public function testIdentityNotInCacheUndefinedError()
    {
        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('critical')
            ->andReturnUsing(function ($code, $params) {
                $this->assertEquals(500, $code);
                $this->assertEquals('PUT', $params[0]);
            })
            ->once()
            ->getMock();

        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn(false)
            ->once()
            ->getMock();

        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 500 Internal Server Error'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );
        $client = (new ClientTest())
            ->setAdapter($adapter);

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $consumer->save(new \DOMElement('element'), '', new ExtractorValidPut());
    }

    /**
     * @tag 103
     */
    public function testIdentityNotInCacheClientError()
    {
        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('error')
            ->andReturnUsing(function ($code, $params) {
                $this->assertEquals(400, $code);
                $this->assertEquals('PUT', $params[0]);
            })
            ->once()
            ->getMock();

        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn(false)
            ->once()
            ->getMock();

        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 400 Bad Request'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );
        $client = (new ClientTest())
            ->setAdapter($adapter);

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $consumer->save(new \DOMElement('element'), '', new ExtractorValidPut());
    }

    /**
     * @tag 104
     */
    public function testIdentityNotInCachePutSuccess()
    {
        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 201 Created'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );

        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('debug')
            ->andReturnUsing(function ($code, $params) {
                $this->assertEquals(201, $code);
                $this->assertEquals('PUT', $params[0]);
                return null;
            })
            ->once()
            ->getMock()
            ->shouldReceive('info')
            ->andReturn(null)
            ->once()
            ->getMock();

        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock()
            ->shouldReceive('setItem')
            ->andReturn(true)
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $response = $consumer->save(new \DOMElement('element'), 'this/key', new ExtractorValidPut());

        $this->assertInternalType('array', $response);
    }

    /**
     * @tag 105
     */
    public function testIdentityNotInCachePutConflictPatchSuccess()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock()
            ->shouldReceive('setItem')
            ->andReturn(true)
            ->once()
            ->getMock();

        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Location: http://localhost:8080/path/to/resource'   . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );
        $adapter->addResponse(
            'HTTP/1.1 204 No Content'   . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );
        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('info')
            ->andReturnUsing(function ($message, $params) {
                $this->assertEquals('Store in cache', $message);
                $this->assertEquals('PATCH', $params[0]);
                return null;
            })
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setClient($client)
            ->setLogger($logger)
            ->setCache($cache)
            ->setUri((new Http('http://localhost:8080')));

        $consumer->save(new \DOMElement('element'), '', new ExtractorValidPut($data));
    }

}
