<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 1/06/2016
 * Time: 10:20 AM
 */
namespace AlthingiAggregator\Lib\Consumer;

include_once __DIR__ . '/Stub/ExtractorExceptionStub.php';
include_once __DIR__ . '/Stub/ExtractorValidPost.php';

use AlthingiAggregator\Lib\Consumer\Stub\ExtractorValidPost;
use Mockery;
use PHPUnit_Framework_TestCase;
use AlthingiAggregator\Lib\Consumer\Stub\ExtractorExceptionStub;
use Zend\Http\Client\Adapter\Test as ClientTestAdapter;
use Zend\Http\Client as ClientTest;

class RestServerConsumerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testExtractorThrowsException()
    {
        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface');
        $client = Mockery::mock('Zend\Http\Client');
        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('error')
            ->andReturnNull()
            ->times(1)
            ->getMock();
        $config = ['server' => ['host' => '']];

        $consumer = (new RestServerConsumer())
            ->setCache($cache)
            ->setClient($client)
            ->setConfig($config)
            ->setLogger($logger);

        $result = $consumer->save(new \DOMElement('element'), '', new ExtractorExceptionStub());

        $this->assertNull($result);
    }

    public function testPostWithCache()
    {
        $config = ['server' => ['host' => '']];
        $entryData = ['data1' => 1, 'data2' => 2];

        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn(md5(implode(',', $entryData)))
            ->times(1)
            ->getMock();
        $client = Mockery::mock('Zend\Http\Client');
        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('debug')
            ->andReturnUsing(function ($message) {
                $this->assertEquals('Entry found in cache - Not sending to Server', $message);
                return null;
            })
            ->getMock();

        $consumer = (new RestServerConsumer())
            ->setCache($cache)
            ->setClient($client)
            ->setConfig($config)
            ->setLogger($logger);

        $resultData = $consumer->save(new \DOMElement('element'), '', new ExtractorValidPost($entryData));

        $this->assertNull($resultData);
    }

    public function testPostWith201()
    {
        $responseCode = 201;
        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn(md5('this-is-not-a-stored-key'))
            ->times(1)
            ->getMock();
        $client = Mockery::mock('Zend\Http\Client')
            ->shouldReceive('send')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('getStatusCode')
            ->andReturn($responseCode)
            ->getMock()
            ->shouldReceive('getContent')
            ->andReturn('')
            ->getMock();

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('debug')
            ->andReturnUsing(function ($code, $params) use ($responseCode) {
                $this->assertEquals($responseCode, $code);
                $this->assertEquals('POST', $params[0]);
                return null;
            })
            ->times(1)
            ->getMock();
        $config = ['server' => ['host' => '']];
        $entryData = ['data1' => 1, 'data2' => 2];

        $consumer = (new RestServerConsumer())
            ->setCache($cache)
            ->setClient($client)
            ->setConfig($config)
            ->setLogger($logger);

        $resultData = $consumer->save(new \DOMElement('element'), '', new ExtractorValidPost($entryData));

        $this->assertEquals($entryData, $resultData);

    }

    public function testPostWith500()
    {
        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn(md5('this-is-not-a-stored-key'))
            ->times(1)
            ->getMock();

        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 500 Internal Server Error'   . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );

        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('warning')
            ->andReturnUsing(function ($code, $params) {
                $this->assertEquals(500, $code);
                $this->assertEquals('POST', $params[0]);
                return null;
            })
            ->times(1)
            ->getMock();

        $config = ['server' => ['host' => '']];
        $entryData = ['data1' => 1, 'data2' => 2];

        $consumer = (new RestServerConsumer())
            ->setCache($cache)
            ->setClient($client)
            ->setConfig($config)
            ->setLogger($logger);

        $resultData = $consumer->save(new \DOMElement('element'), '', new ExtractorValidPost($entryData));

        $this->assertEquals($entryData, $resultData);
    }

    public function testPostWith409NoLocationHeader()
    {
        $responseCode = 409;
        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn(md5('this-is-not-a-stored-key'))
            ->times(1)
            ->getMock();
        $client = Mockery::mock('Zend\Http\Client')
            ->shouldReceive('send')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('getStatusCode')
            ->andReturn($responseCode)
            ->getMock()
            ->shouldReceive('getHeaders')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('get')
            ->andReturnNull()
            ->getMock();

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('warning')
            ->andReturnUsing(function ($code, $params) use ($responseCode) {
                $this->assertEquals($responseCode, $code);
                $this->assertEquals('POST', $params[0]);
                $this->assertEquals('No Location header', $params[3]);
            })
            ->getMock();

        $config = ['server' => ['host' => '']];
        $entryData = ['data1' => 1, 'data2' => 2];

        $consumer = (new RestServerConsumer())
            ->setCache($cache)
            ->setClient($client)
            ->setConfig($config)
            ->setLogger($logger);

        $resultData = $consumer->save(new \DOMElement('element'), '', new ExtractorValidPost($entryData));

        $this->assertEquals($entryData, $resultData);

    }

    public function testPostWith409LocationHeaderSuccess()
    {
        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn(md5('this-is-not-a-stored-key'))
            ->times(1)
            ->getMock()
            ->shouldReceive('setItem')
            ->andReturnSelf()
            ->times(1)
            ->getMock();

        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );

        $adapter->addResponse(
            'HTTP/1.1 200 OK'         . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );


        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('debug')
            ->andReturnNull()
            ->times(2)
            ->getMock();

        $config = ['server' => ['host' => '']];
        $entryData = ['data1' => 1, 'data2' => 2];

        $consumer = (new RestServerConsumer())
            ->setCache($cache)
            ->setClient($client)
            ->setConfig($config)
            ->setLogger($logger);

        $resultData = $consumer->save(new \DOMElement('element'), '', new ExtractorValidPost($entryData));

        $this->assertEquals($entryData, $resultData);
    }

    public function testPostWith409LocationHeaderUnSuccessful()
    {
        $cache = Mockery::mock('Zend\Cache\Storage\StorageInterface')
            ->shouldReceive('getItem')
            ->andReturn(md5('this-is-not-a-stored-key'))
            ->times(1)
            ->getMock();

        $adapter = new ClientTestAdapter();
        $adapter->setResponse(
            'HTTP/1.1 409 Conflict'   . "\r\n" .
            'Location: /'             . "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );

        $adapter->addResponse(
            'HTTP/1.1 400 Bad Request'. "\r\n" .
            'Content-Type: text/html' . "\r\n\r\n"
        );


        $client = (new ClientTest())
            ->setAdapter($adapter);

        $logger = Mockery::mock('Psr\Log\LoggerInterface')
            ->shouldReceive('warning')
            ->andReturnNull()
            ->times(1)
            ->getMock();

        $config = ['server' => ['host' => '']];
        $entryData = ['data1' => 1, 'data2' => 2];

        $consumer = (new RestServerConsumer())
            ->setCache($cache)
            ->setClient($client)
            ->setConfig($config)
            ->setLogger($logger);

        $resultData = $consumer->save(new \DOMElement('element'), '', new ExtractorValidPost($entryData));

        $this->assertEquals($entryData, $resultData);
    }
}
