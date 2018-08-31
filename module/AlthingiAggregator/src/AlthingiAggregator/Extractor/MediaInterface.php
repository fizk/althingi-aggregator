<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 4/04/2016
 * Time: 5:34 PM
 */

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
