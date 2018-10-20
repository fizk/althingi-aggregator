<?php
namespace AlthingiAggregator\Lib\Consumer;

use AlthingiAggregator\Extractor\Exception;
use DOMElement;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use AlthingiAggregator\Lib\ConfigAwareInterface;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\ExtractionInterface;

class NullConsumer implements
    ConsumerInterface,
    ConfigAwareInterface,
    LoggerAwareInterface
{
    /** @var  array */
    private $config;

    /** @var  \Psr\Log\LoggerInterface */
    private $logger;

    /**
     * Send $extract to REST server via HTTP.
     *
     * @param DOMElement $element
     * @param string $api
     * @param ExtractionInterface $extract
     * @return array
     */
    public function save(DOMElement $element, $api, ExtractionInterface $extract)
    {
        $data = null;
        try {
            $data = $extract->extract($element);
            if ($extract instanceof IdentityInterface) {
                $api = sprintf('%s/%s', $api, $extract->getIdentity());
            }
            $this->logger->info($api, $data);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), [$api]);
        }

        return $data;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
}
