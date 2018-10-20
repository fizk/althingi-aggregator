<?php
namespace AlthingiAggregator\Lib;

interface ConfigAwareInterface
{
    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config);
}
