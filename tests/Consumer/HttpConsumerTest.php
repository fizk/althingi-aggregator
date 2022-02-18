<?php
namespace App\Consumer;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{ResponseInterface, RequestInterface};
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Uri;
use Laminas\Cache\Storage\StorageInterface;
use Mockery;
use App\Consumer\HttpConsumer;
use App\Consumer\Stub\{ExtractorValidPost, ExtractorValidPut};

class HttpConsumerTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @tag 001
     */
    public function testNoIdentityNotInCacheUndefinedError()
    {
        $client = new class implements ClientInterface
        {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new EmptyResponse(500);
            }
        };

        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $response = $consumer->save(
            'this/key',
            (new ExtractorValidPost())->populate(new \DOMElement('element'))
        );

        $this->assertIsArray($response);
    }

    /**
     * @tag 002
     */
    public function testNoIdentityNotInCacheClientError()
    {
        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock();
        $client = new class implements ClientInterface
        {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new EmptyResponse(400);
            }
        };
        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $response = $consumer->save(
            'this/key',
            (new ExtractorValidPost())->populate(new \DOMElement('element'))
        );

        $this->assertIsArray($response);
    }

    /**
     * @tag 003
     */
    public function testNoIdentityNotInCachePostSuccess()
    {
        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock()
            ->shouldReceive('setItem')
            ->andReturn(true)
            ->once()
            ->getMock();
        $client = new class implements ClientInterface
        {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new EmptyResponse(201);
            }
        };
        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $response = $consumer->save(
            'this/key',
            (new ExtractorValidPost())->populate(new \DOMElement('element'))
        );

        $this->assertIsArray($response);
    }

    /**
     * @tag 004
     */
    public function testNoIdentityInCache()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn(HttpConsumer::createStorageValue($data))
            ->once()
            ->getMock();
        $client = new class implements ClientInterface
        {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new EmptyResponse(201);
            }
        };
        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $consumer->save(
            '',
            (new ExtractorValidPost($data))->populate(new \DOMElement('element'))
        );
        $this->assertTrue(true); //@todo
    }

    /**
     * @tag 006
     */
    public function testNoIdentityNotInCachePostConflictHasLocationHeaderUndefinedError()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->twice()
            ->getMock();
        $client = new class implements ClientInterface
        {
            private int $iteration = 0;
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                switch($this->iteration) {
                    case 0:
                        $this->iteration = 1;
                        return new EmptyResponse(409, ['Location' => '/path/to/resource']);
                    default:
                        return new EmptyResponse(500);
                }
            }
        };
        $consumer = (new HttpConsumer())
            ->setHttpClient($client)

            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $consumer->save(
            '',
            (new ExtractorValidPost($data))->populate(new \DOMElement('element'))
        );
        $this->assertTrue(true); //@todo
    }

    /**
     * @tag 007
     */
    public function testNoIdentityNotInCachePostConflictHasLocationHeaderSuccess()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->twice()
            ->getMock()
            ->shouldReceive('setItem')
            ->andReturn(true)
            ->once()
            ->getMock();


        $client = new class implements ClientInterface
        {
            private int $iteration = 0;
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                switch ($this->iteration) {
                    case 0:
                        $this->iteration = 1;
                        return new EmptyResponse(409, ['Location' => '/path/to/resource']);
                    default:
                        return new EmptyResponse(204);
                }
            }
        };
        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $consumer->save(
            '',
            (new ExtractorValidPost($data))->populate(new \DOMElement('element'))
        );
        $this->assertTrue(true); //@todo
    }

    /**
     * @tag 008
     */
    public function testNoIdentityNotInCachePostConflictHasLocationHeaderResourceNotFound()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->twice()
            ->getMock();
        $client = new class implements ClientInterface
        {
            private int $iteration = 0;
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                switch ($this->iteration) {
                    case 0:
                        $this->iteration = 1;
                        return new EmptyResponse(409, ['Location' => '/path/to/resource']);
                    default:
                        return new EmptyResponse(404);
                }
            }
        };
        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $consumer->save(
            '',
            (new ExtractorValidPost($data))->populate(new \DOMElement('element'))
        );
        $this->assertTrue(true); //@todo
    }

    /**
     * @tag 101
     */
    public function testIdentityInCache()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn(HttpConsumer::createStorageValue($data))
            ->once()
            ->getMock();
        $client = new class implements ClientInterface
        {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new EmptyResponse(201);
            }
        };
        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $consumer->save(
            '',
            (new ExtractorValidPut($data))->populate(new \DOMElement('element'))
        );
        $this->assertTrue(true); //@todo
    }

    public function testUniqueInCache()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $client = new class implements ClientInterface
        {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new EmptyResponse(201);
            }
        };
        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn(HttpConsumer::createStorageValue($data))
            ->once()
            ->getMock();

        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $consumer->save(
            '',
            (new ExtractorValidPost($data))->populate(new \DOMElement('element'))
        );
        $this->assertTrue(true); //@todo
    }

    /**
     * @tag 102
     */
    public function testIdentityNotInCacheUndefinedError()
    {
        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn(false)
            ->once()
            ->getMock();
        $client = new class implements ClientInterface
        {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new EmptyResponse(500);
            }
        };
        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $consumer->save(
            '',
            (new ExtractorValidPut())->populate(new \DOMElement('element'))
        );
        $this->assertTrue(true); //@todo
    }

    /**
     * @tag 103
     */
    public function testIdentityNotInCacheClientError()
    {
        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn(false)
            ->once()
            ->getMock();
        $client = new class implements ClientInterface
        {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new EmptyResponse(400);
            }
        };
        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $consumer->save(
            '',
            (new ExtractorValidPut())->populate(new \DOMElement('element'))
        );
        $this->assertTrue(true); //@todo
    }

    /**
     * @tag 104
     */
    public function testIdentityNotInCachePutSuccess()
    {
        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->once()
            ->getMock()
            ->shouldReceive('setItem')
            ->andReturn(true)
            ->once()
            ->getMock();
        $client = new class implements ClientInterface
        {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new EmptyResponse(201);
            }
        };
        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $response = $consumer->save(
            'this/key',
            (new ExtractorValidPut())->populate(new \DOMElement('element'))
        );

        $this->assertIsArray($response);
    }

    /**
     * @tag 105
     */
    public function testIdentityNotInCachePutConflictPatchSuccess()
    {
        $data = ['key1' => 1, 'key2' => 2];

        $cache = Mockery::mock(StorageInterface::class)
            ->shouldReceive('getItem')
            ->andReturn('no-matching-value')
            ->twice()
            ->getMock()
            ->shouldReceive('setItem')
            ->andReturn(true)
            ->once()
            ->getMock();
        $client = new class implements ClientInterface
        {
            private int $iteration = 0;
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                switch ($this->iteration) {
                    case 0:
                        $this->iteration = 1;
                        return new EmptyResponse(409, ['Location' => '/path/to/resource']);
                    default:
                        return new EmptyResponse(204);
                }
            }
        };
        $consumer = (new HttpConsumer())
            ->setHttpClient($client)
            ->setCache($cache)
            ->setUri((new Uri('http://localhost:8080')));

        $consumer->save(
            '',
            (new ExtractorValidPut($data))->populate(new \DOMElement('element'))
        );
        $this->assertTrue(true); //@todo
    }
}
