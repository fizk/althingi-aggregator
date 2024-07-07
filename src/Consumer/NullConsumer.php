<?php

namespace App\Consumer;

use App\Lib\{IdentityInterface, LoggerAwareInterface};
use App\Extractor\ExtractionInterface;
use Psr\Log\{LoggerInterface};

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

    public function setLogger(LoggerInterface $logger): static
    {
        $this->logger = $logger;
        return $this;
    }
}
