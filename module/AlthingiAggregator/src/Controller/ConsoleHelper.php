<?php
namespace AlthingiAggregator\Controller;

use DOMXPath;
use DOMNodeList;
use DOMElement;
use AlthingiAggregator\Extractor\ExtractionInterface;
use AlthingiAggregator\Consumer\ConsumerInterface;
use AlthingiAggregator\Provider\ProviderInterface;

trait ConsoleHelper
{
    /** @var  array */
    private $config;

    /** @var  ConsumerInterface */
    private $consumer;

    /** @var  ProviderInterface */
    private $provider;

    /** @var  \Zend\Http\Client */
    private $althingiClient;

    /** @var  \Zend\Http\Client */
    private $restClient;

    /**
     * Query for and save document, all in one go.
     *
     * Provide a URL to where the data is stored, the kay to save
     * the document under, and xPath to the element(s) to save from the
     * document and an extractor... this method will do all the work
     * for you.
     *
     * @param $url
     * @param $storageKey
     * @param $xPath
     * @param ExtractionInterface $extract
     * @throws \Exception
     */
    private function queryAndSave($url, $storageKey, $xPath, ExtractionInterface $extract)
    {
        $dom = $this->queryForDocument($url);
        $xPathObject = new \DOMXPath($dom);
        $elements = $xPathObject->query($xPath);
        $this->saveDomNodeList($elements, $storageKey, $extract);
    }

    /**
     * Save a DOMNodeList.
     *
     * Shorthand, so you don't have to call self::saveDomElement in a loop
     * in your code.
     *
     * @param DOMNodeList $elements
     * @param $storageKey
     * @param ExtractionInterface $extract
     * @see self::saveDomElement
     */
    private function saveDomNodeList(DOMNodeList $elements, $storageKey, ExtractionInterface $extract)
    {
        foreach ($elements as $element) {
            $this->saveDomElement($element, $storageKey, $extract);
        }
    }

    /**
     * Save one DOMElement.
     *
     * @param DOMElement $element
     * @param $storageKey
     * @param ExtractionInterface $extract
     */
    private function saveDomElement(DOMElement $element, $storageKey, ExtractionInterface $extract)
    {
        $this->consumer->save($element, $storageKey, $extract);
    }

    /**
     * Get one DOMDocument from the provider.
     *
     * @param $url
     * @param callable $cb
     * @return \DOMDocument
     * @throws \Exception
     */
    private function queryForDocument($url, callable $cb = null)
    {
        return $this->provider->get($url, $cb);
    }

    /**
     * Query for DOMNodeList.
     *
     * This is almost the same as self::queryForDocument but adds the ability
     * to DOMXpath query the incoming DOMDocument element.
     *
     * This is a little shorthand method so you don't have to create a
     * DOMXpath object in your code.
     *
     * @param $url
     * @param callable $cb
     * @param $xPath
     * @return \DOMNodeList
     * @throws \Exception
     */
    private function queryForNoteList($url, $xPath, callable $cb = null)
    {
        $dom = $this->queryForDocument($url, $cb);
        $xpath = new DOMXpath($dom);
        return $xpath->query($xPath);
    }

    /**
     * Set a consumer.
     *
     * See the Service manager for details.
     *
     * @param ConsumerInterface $consumer
     * @return $this
     */
    public function setConsumer(ConsumerInterface $consumer)
    {
        $this->consumer = $consumer;
        return $this;
    }

    /**
     * Set a provider.
     *
     * See the Service manager for details.
     *
     * @param ProviderInterface $provider
     * @return $this
     */
    public function setProvider(ProviderInterface $provider)
    {
        $this->provider = $provider;
        return $this;
    }
}
