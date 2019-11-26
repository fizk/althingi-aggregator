<?php
namespace AlthingiAggregator\Provider;

interface ProviderAwareInterface
{
    /**
     * @param ProviderInterface $provider
     * @return $this
     */
    public function setProvider(ProviderInterface $provider);
}
