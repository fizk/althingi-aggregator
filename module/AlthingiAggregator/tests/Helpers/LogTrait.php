<?php

namespace AlthingiAggregatorTest\Helpers;

use Monolog\Handler;

trait LogTrait
{
    private $pattern = '/(\[[0-9\-: ]+\]) (logger\.(ERROR|INFO):) (([0-9]{1,3}) \["(EXCEPTION|HTTP|POST|PATCH|GET|PUT|PROVIDER_CACHE|CONSUMER|CONSUMER_CACHE)",".*",(\[.*\]|{.*})(,".*")?\]) (\[.*\])/';

    public function assertLogHandler(Handler\TestHandler $logs, $pattern = null)
    {
        $pattern = $pattern ?: $this->pattern;
        $records = array_map(function ($record) {
            return $record['formatted'];
        }, $logs->getRecords());

        $isAllLogsValid = self::every($records, function ($entry) use ($pattern) {
            return preg_match($pattern, $entry) === 1;
        });

        $this->assertTrue($isAllLogsValid);
    }

    public static function every(array $array, callable $fn)
    {
        foreach ($array as $value) {
            if (! $fn($value)) {
                return false;
            }
        }
        return true;
    }
}