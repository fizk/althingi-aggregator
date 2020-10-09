<?php
namespace App\Consumer;

use Psr\Log\{LoggerInterface, LoggerAwareInterface};
use App\Lib\{ConfigAwareInterface, IdentityInterface};
use App\Extractor\ExtractionInterface;
use DOMElement;

class NullConsumer implements ConsumerInterface, ConfigAwareInterface, LoggerAwareInterface
{
    private array $config;
    private LoggerInterface $logger;

    public function save(DOMElement $element, string $api, ExtractionInterface $extract): ?array
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

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }
}
