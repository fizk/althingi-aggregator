<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 29/03/2016
 * Time: 10:30 AM
 */
namespace AlthingiAggregator\Lib\Http\Client\Adapter;

use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;

class LocalXmlAdapterTest extends PHPUnit_Framework_TestCase
{
    /** @var  \org\bovigo\vfs\vfsStream */
    private $root;

    public function setUp()
    {
        $this->root = vfsStream::setup('home.is');
        vfsStream::newFile('xml-file.xml')
            ->at($this->root)
            ->setContent("The new contents of the file");
    }

    public function tearDown()
    {
        $this->root = null;
    }

    public function xtestGetFileFound()
    {
        $request = new Request();
        $request->setMethod('get')->setUri('http://home.is/xml-file');
        $response = (new Client('some-url', ['adapter' => new LocalXmlAdapter()]))
            ->setOptions(['protocol' => 'vfs://'])
            ->send($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function xtestGetFileNOtFound()
    {
        $request = new Request();
        $request->setMethod('get')
            ->setUri('http://home.is/file-does-not-exist');

        $response = (new Client('some-url', ['adapter' => new LocalXmlAdapter()]))
            ->setOptions(['protocol' => 'vfs://'])
            ->send($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testPost()
    {
        $request = new Request();
        $request->setMethod('post')
            ->setUri('http://home.is/posting-a-file')
            ->setPost(new Parameters(['hundur' => 'snati']));

        $response = (new Client('some-url', ['adapter' => new LocalXmlAdapter()]))
            ->setOptions(['protocol' => 'vfs://'])
            ->send($request);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
