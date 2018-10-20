<?php
namespace AlthingiAggregator\Lib\Provider;

interface ProviderAwareInterface
{
    /**
     * @param ProviderInterface $provider
     * @return $this
     */
    public function setProvider(ProviderInterface $provider);
}
