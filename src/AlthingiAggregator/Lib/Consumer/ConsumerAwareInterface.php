<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 3/04/2016
 * Time: 5:06 PM
 */

namespace AlthingiAggregator\Lib\Consumer;

interface ConsumerAwareInterface
{
    /**
     * @param ConsumerInterface $consumer
     * @return $this
     */
    public function setConsumer(ConsumerInterface $consumer);
}
