<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 3/04/2016
 * Time: 5:22 PM
 */

namespace AlthingiAggregator\Lib\Provider;

interface ProviderAwareInterface
{
    /**
     * @param ProviderInterface $provider
     * @return $this
     */
    public function setProvider(ProviderInterface $provider);
}
