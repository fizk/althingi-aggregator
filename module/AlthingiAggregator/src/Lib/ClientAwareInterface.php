<?php
namespace AlthingiAggregator\Lib;

use \Zend\Http\Client;

interface ClientAwareInterface
{
    /**
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client);
}
