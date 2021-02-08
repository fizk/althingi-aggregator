<?php
namespace App\Consumer;

use Psr\Log\{LoggerInterface, LoggerAwareInterface};
use App\Lib\IdentityInterface;
use App\Extractor\ExtractionInterface;
use DOMElement;

class NullConsumer implements ConsumerInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function save(string $api, ExtractionInterface $extract): ?array
    {
        $data = null;
        try {
            $data = $extract->extract();
            if ($extract instanceof IdentityInterface) {
                $api = sprintf('%s/%s', $api, $extract->getIdentity());
            }
            $this->logger->info($api, $data);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), [$api]);
        }

        return $data;
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }
}
