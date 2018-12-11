<?php
namespace AlthingiAggregator\Lib;

class GovernmentDocumentCallback
{
    public function __invoke(string $content): \DOMDocument
    {
        $pageDom = new \DOMDocument();
        $utf8content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
        @$pageDom->loadHTML($utf8content);

        $xpath = new \DOMXpath($pageDom);
        $list = $xpath->query('//*[@id="main"]/div[5]/div[2]/ul')->item(0)->getElementsByTagName('li');

        $tmpDom = new \DOMDocument();
        foreach ($list as $i) {
            $ti = $tmpDom->importNode($i, true);
            $tmpDom->appendChild($ti);
        }

        $list = $xpath->query('//*[@id="main"]/div[6]/div[2]/ul')->item(0)->getElementsByTagName('li');
        foreach ($list as $i) {
            $ti = $tmpDom->importNode($i, true);
            $tmpDom->appendChild($ti);
        }

        $dom = new \DOMDocument();
        $rootElement = $dom->createElement('root');
        $dom->appendChild($rootElement);
        foreach ($tmpDom->childNodes as $item) {
            $regexResults = [];
            preg_match_all(
                '/([0-9]{0,2})\.? '.
                '(janúar|febrúar|mars|apríl|maí|júní|júlí|ágúst|september|október|nóvember|desember)\.? '.
                '*([0-9]{4})/',
                $item->getElementsByTagName('div')->item(1)->nodeValue,
                $regexResults,
                PREG_SET_ORDER
            );

            $i = $dom->createElement('item', $item->getElementsByTagName('span')->item(0)->nodeValue);
            $i->setAttribute(
                'from',
                "{$regexResults[0][3]}-{$this->monthToNumber($regexResults[0][2])}".
                "-{$this->zeroSetDay($regexResults[0][1])}"
            );

            if (isset($regexResults[1])) {
                $i->setAttribute(
                    'to',
                    "{$regexResults[1][3]}-{$this->monthToNumber($regexResults[1][2])}".
                    "-{$this->zeroSetDay($regexResults[1][1])}"
                );
            }
            $rootElement->appendChild($i);
        }


        // If `to` attribute is missing
        $items = $dom->getElementsByTagName('item');
        for ($i = 0; $i < $items->length; $i++) {
            if ($i !== 0) {
                if (! $items->item($i)->hasAttribute('to')) {
                    $items->item($i)->setAttribute(
                        'to',
                        $items->item($i - 1)->getAttribute('from')
                    );
                }
            }
        }

        return $dom;
    }

    private function monthToNumber($name)
    {
        $map = [
            'janúar'    => '01',
            'febrúar'   => '02',
            'mars'      => '03',
            'apríl'     => '04',
            'maí'       => '05',
            'júní'      => '06',
            'júlí'      => '07',
            'ágúst'     => '08',
            'september' => '09',
            'október'   => '10',
            'nóvember'  => '11',
            'desember'  => '12',
        ];

        return $map[$name];
    }

    private function zeroSetDay($day)
    {
        return $day > 9 ? (string) $day : "0{$day}";
    }
}
