<?php
namespace App\Handler;

use App\Extractor\ExtractionInterface;
use App\Consumer\ConsumerInterface;
use App\Provider\ProviderInterface;
use DOMXPath;
use DOMNodeList;
use DOMElement;

trait ConsoleHelper
{
    private array $config;

    private ConsumerInterface $consumer;

    private ProviderInterface $provider;

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
        $this->consumer->save($storageKey, clone $extract->populate($element));
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

    public function setConsumer(ConsumerInterface $consumer): self
    {
        $this->consumer = $consumer;
        return $this;
    }

    public function setProvider(ProviderInterface $provider): self
    {
        $this->provider = $provider;
        return $this;
    }
}
