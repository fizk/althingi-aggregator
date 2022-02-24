<?php

namespace App\Handler\Committee;

use App\Handler\Committee\Find;
use App\Extractor\ExtractionInterface;
use App\Consumer\TestConsumer;
use App\Provider\NullProvider;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use DOMDocument;
use DOMNodeList;

class FindTest extends TestCase
{
    public function testTrue()
    {
        $handler = new class($this) extends Find
        {
            private TestCase $testcase;
            public function __construct(TestCase $testcase)
            {
                $this->testcase = $testcase;
            }
            protected function queryForDocument($url, callable $cb = null)
            {
                $dom = new DOMDocument();
                $dom->loadXML('<?xml version="1.0" encoding="UTF-8"?>
                    <nefndir>
                        <nefnd id="201">
                            <heiti>allsherjar- og menntamálanefnd</heiti>
                            <skammstafanir>
                                <stuttskammstöfun>am</stuttskammstöfun>
                                <löngskammstöfun>allsh.- og menntmn.</löngskammstöfun>
                            </skammstafanir>
                            <tímabil>
                                <fyrstaþing>140</fyrstaþing>
                            </tímabil>
                            </nefnd>
                    </nefndir>
                ');
                return $dom;
            }

            protected function saveDomNodeList(
                DOMNodeList $elements,
                $storageKey,
                ExtractionInterface $extract
            ){
                $expected = [
                    [
                        'committee_id' => '201',
                        'name' => 'allsherjar- og menntamálanefnd',
                        'first_assembly_id' => '140',
                        'last_assembly_id' => null,
                        'abbr_short' => 'am',
                        'abbr_long' => 'allsh.- og menntmn.',
                    ],
                    [
                        'committee_id' => '-1',
                        'name' => 'óskylgreind nefnd',
                        'first_assembly_id' => '1',
                        'last_assembly_id' => null,
                        'abbr_short' => null,
                        'abbr_long' => null,
                    ],
                ];
                $actual = array_map(
                    fn($element) => $extract->populate($element)->extract(),
                    iterator_to_array($elements)
                );

                $this->testcase->assertEquals($expected, $actual);
            }
        };

        $handler->setProvider(new NullProvider());
        $handler->setConsumer(new TestConsumer());

        $request = (new ServerRequest())->withUri(new Uri("/command"));

        $handler->handle($request);
    }
}
