<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 21/06/15
 * Time: 4:44 PM
 */

namespace AlthingiAggregator\Controller;

use DOMXPath;
use Psr\Log\LoggerInterface;
use AlthingiAggregator\Lib\Http\DomClient;
use AlthingiAggregator\Lib\IdentityInterface;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\Extractor\ExtractionInterface;

trait ConsoleHelper
{
    private $logger;

    /**
     * Extract a collection of resources and PUTs them into REST service.
     *
     * @param string $name This is just for the logger
     * @param string $url Full URL to Althingi XML service
     * @param string $api API endpoint key
     * @param string $elementName name of XML element that holds a single resource
     * @param ExtractionInterface $extract
     * @throws \Exception
     */
    private function singleLevelGet($name, $url, $api, $elementName, ExtractionInterface $extract)
    {
        $this->getLogger()->info("{$name} -- start");
        $dom = (new DomClient())
            ->setClient($this->getClient())
            ->get($url);
        $this->singleLevelPut($dom, $api, $elementName, $extract);
        $this->getLogger()->info("{$name} -- end");
    }

    private function singleLevelPut(\DOMDocument $dom, $api, $elementName, ExtractionInterface $extract)
    {
        foreach ($dom->getElementsByTagName($elementName) as $element) {
            $this->singleElementProcess($element, $api, $extract);
        }
    }

    private function singleElementProcess(\DOMElement $element, $api, ExtractionInterface $extract)
    {
        $config = $this->getServiceLocator()->get('Config');
        try {
            $entry = $extract->extract($element);
            if ($extract instanceof IdentityInterface) {
                $apiRequest = (new Request())
                    ->setMethod('post')
                    ->setHeaders((new Headers())->addHeaders([
                        'X-HTTP-Method-Override' => 'PUT'
                    ]))
                    ->setUri(sprintf('%s/%s/%s', $config['server']['host'], $api, $extract->getIdentity()))
                    ->setPost(new Parameters($entry));

                $apiResponse = $this->getClient()->send($apiRequest);

                if (201 == $apiResponse->getStatusCode()) {
                    $this->getLogger()->info($apiResponse->getStatusCode());
                } elseif (409 == $apiResponse->getStatusCode()) {
                    $patchRequest = (new Request())
                        ->setMethod('post')
                        ->setHeaders((new Headers())->addHeaders([
                            'X-HTTP-Method-Override' => 'PATCH'
                        ]))
                        ->setUri(sprintf('%s/%s/%s', $config['server']['host'], $api, $extract->getIdentity()))
                        ->setPost(new Parameters($entry));

                    $patchResponse = $this->getClient()->send($patchRequest);
                    if ((int) ($patchResponse->getStatusCode()/100) != 2) {
                        $this->getLogger()->error(
                            $patchResponse->getStatusCode(),
                            [$patchResponse->getContent()]
                        );
                    } else {
                        $this->getLogger()->info(
                            $patchResponse->getStatusCode(),
                            [$patchResponse->getContent()]
                        );
                    }
                } else {
                    $this->getLogger()->error(
                        $apiResponse->getStatusCode(),
                        [$apiResponse->getContent()]
                    );
                }
            }

        } catch (\Exception $e) {
            $this->getLogger()->error($this->getClient()->getUri() . ' -> ' . $e->getMessage());
        }
    }

    private function queryForDocument($url)
    {
        return (new DomClient())
            ->setClient($this->getClient())
            ->get($url);
    }

    private function queryForNoteList($url, $xPath)
    {
        $dom = $this->queryForDocument($url);
        $xpath = new DOMXpath($dom);
        return $xpath->query($xPath);
    }

    /**
     * @return \Zend\Http\Client
     */
    private function getClient()
    {
        return $this->getServiceLocator()->get('HttpClient');
    }

    /**
     * @param LoggerInterface $logger
     * @return mixed
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if (!$this->logger) {
            $this->setLogger(
                $this->getServiceLocator()->get('Psr\Log')
            );
        }
        return $this->logger;
    }
}
