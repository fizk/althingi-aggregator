<?php

namespace App\Callback;

use App\Callback\GovernmentDocumentCallback;
use PHPUnit\Framework\TestCase;

class GovernmentDocumentCallbackTest extends TestCase
{
    public function testHasToAttribute()
    {
        $callback = new GovernmentDocumentCallback();
        $dom = $callback(file_get_contents(__DIR__ . '/data/governments.html'));

        $items = $dom->getElementsByTagName('item');

        for ($i = 0; $i < $items->length; $i++) {
            if ($i !== 0 && ! $items->item($i)->hasAttribute('to')) {
                $this->fail('Missing `to` attribute');
            }
        }

        $this->assertTrue(true);
    }
}
