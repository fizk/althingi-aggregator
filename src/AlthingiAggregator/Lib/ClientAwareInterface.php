<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/06/15
 * Time: 10:11 PM
 */

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
