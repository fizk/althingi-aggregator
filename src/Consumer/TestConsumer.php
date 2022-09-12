<?php
namespace App\Consumer;

use App\Extractor\ExtractionInterface;

class TestConsumer implements ConsumerInterface
{

    public function save(string $api, ExtractionInterface $extract): ?array
    {
        return [];
    }
}
