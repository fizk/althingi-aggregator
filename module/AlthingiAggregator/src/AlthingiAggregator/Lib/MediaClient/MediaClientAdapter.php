<?php
namespace AlthingiAggregator\Lib\MediaClient;

interface MediaClientAdapter
{
    /**
     * @param string $file
     * @param string $slug
     * @param string $contentType
     * @return int
     * @throws \Exception
     */
    public function save($file, $slug, $contentType);
}
