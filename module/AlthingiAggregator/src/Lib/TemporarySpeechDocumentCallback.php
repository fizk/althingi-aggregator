<?php
namespace AlthingiAggregator\Lib;

class TemporarySpeechDocumentCallback
{
    /** @var string */
    private $domain;

    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    public function __invoke(string $src)
    {
        $result = [];
        preg_match_all('/(href=\')(rad[0-9T]*\.xml)(\')/', $src, $result);

        $dom = new \DOMDocument();
        $root = $dom->createElement('root');
        $dom->appendChild($root);
        foreach ($result[2] as $name) {
            $root->appendChild($dom->createElement('item', "{$this->domain}/{$name}"));
        }

        return $dom;
    }
}
