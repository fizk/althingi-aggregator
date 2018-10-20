<?php
namespace AlthingiAggregator\Lib\Consumer;

interface ConsumerAwareInterface
{
    /**
     * @param ConsumerInterface $consumer
     * @return $this
     */
    public function setConsumer(ConsumerInterface $consumer);
}
