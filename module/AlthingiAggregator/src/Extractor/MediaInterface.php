<?php
namespace AlthingiAggregator\Extractor;

interface MediaInterface
{
    /**
     * @return string
     */
    public function getSlug();

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @return string
     */
    public function getContentType();
}
