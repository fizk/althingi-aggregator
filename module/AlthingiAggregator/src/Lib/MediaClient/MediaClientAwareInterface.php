<?php
namespace AlthingiAggregator\Lib\MediaClient;

use \AlthingiAggregator\Lib\MediaClient\MediaClientAdapter;

interface MediaClientAwareInterface
{
    public function setMediaClient(MediaClientAdapter $mediaClient);
}
