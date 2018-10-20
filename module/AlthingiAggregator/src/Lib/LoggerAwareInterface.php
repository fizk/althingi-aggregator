<?php
namespace AlthingiAggregator\Lib;

use Psr\Log\LoggerInterface;

interface LoggerAwareInterface
{
    /**
     * @param LoggerInterface $logger
     * @return mixed
     */
    public function setLogger(LoggerInterface $logger);
}
