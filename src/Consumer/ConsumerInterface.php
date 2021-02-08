<?php
namespace App\Consumer;

use App\Extractor\ExtractionInterface;

interface ConsumerInterface
{
    public function save(string $storageKey, ExtractionInterface $extract);
}
