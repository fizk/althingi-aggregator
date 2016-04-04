<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 31/03/2016
 * Time: 6:13 PM
 */

namespace AlthingiAggregator\Lib\Http;

interface ConfigAwareInterface
{
    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config);
}
