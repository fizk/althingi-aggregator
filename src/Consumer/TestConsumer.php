<?php
namespace App\Consumer;

use Psr\Log\{LoggerInterface, LoggerAwareInterface};
use App\Lib\IdentityInterface;
use App\Extractor\ExtractionInterface;
use DOMElement;

class TestConsumer implements ConsumerInterface
{

    public function save(string $api, ExtractionInterface $extract): ?array
    {
        return [];
    }
}
