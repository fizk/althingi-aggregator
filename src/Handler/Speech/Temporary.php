<?php
namespace App\Handler\Speech;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\TextResponse;
use App\Extractor;
use App\Callback\TemporarySpeechDocumentCallback;
use App\Consumer\ConsumerAwareInterface;
use App\Provider\ProviderAwareInterface;
use App\Handler\ConsoleHelper;
use DOMDocument;
use DOMElement;

class Temporary implements RequestHandlerInterface, ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyNumber = $request->getAttribute('assembly');
        $url = "https://www.althingi.is/xml/{$assemblyNumber}/raedur_bradabirgda";
        $speechUrlNodeList = $this->queryForNoteList(
            $url,
            '//root/item',
            new TemporarySpeechDocumentCallback($url)
        );

        foreach ($speechUrlNodeList as $item) {
            $this->processTemporarySpeechUrNode($item);
        }

        return new TextResponse(self::class);
    }

    private function processTemporarySpeechUrNode(DOMElement $item)
    {
        $issueUrlPrefix = "https://www.althingi.is/altext/xml/thingmalalisti";

        $speechDocument = $this->queryForDocument($item->nodeValue);

        $time = $speechDocument->getElementsByTagName('umsýsla')->item(0)->getAttribute('tími');
        $category = $speechDocument->getElementsByTagName('mál')->item(0)->getAttribute('málsflokkur');
        $issueNumber = $speechDocument->getElementsByTagName('mál')->item(0)->getAttribute('nr');
        $assemblyNumber = $speechDocument->getElementsByTagName('umsýsla')->item(0)->getAttribute('lgþ');

        $issueDocument = $this->queryForDocument(
            $category === 'A'
                ? "{$issueUrlPrefix}/thingmal/?lthing={$assemblyNumber}&malnr={$issueNumber}"
                : "{$issueUrlPrefix}/bmal/?lthing={$assemblyNumber}&malnr={$issueNumber}"
        );

        $issueSpeechElementList = array_filter(
            iterator_to_array($issueDocument->getElementsByTagName('ræða')),
            function (DOMElement $element) use ($time) {
                return $element->getElementsByTagName('ræðahófst')->item(0)->nodeValue === $time;
            }
        );

        if (count($issueSpeechElementList) === 1) {
            $temporarySpeechDocument = new DOMDocument();
            $rootElement = $temporarySpeechDocument
                ->importNode(array_pop($issueSpeechElementList), true);
            $issueElement = $temporarySpeechDocument
                ->importNode($speechDocument->getElementsByTagName('mál')->item(0), true);
            $speechElement = $temporarySpeechDocument
                ->importNode($speechDocument->getElementsByTagName('ræðutexti')->item(0), true);
            $temporarySpeechDocument->appendChild($rootElement);
            $temporarySpeechDocument->documentElement->appendChild($issueElement);
            $temporarySpeechDocument->documentElement->appendChild($speechElement);
            $temporarySpeechDocument->documentElement->setAttribute('þingmál', $issueNumber);
            $temporarySpeechDocument->documentElement->setAttribute('temporary', 'yes');
            $category = strtolower($category);
            $this->saveDomElement(
                $temporarySpeechDocument->documentElement,
                "loggjafarthing/{$assemblyNumber}/thingmal/{$category}/{$issueNumber}/raedur",
                new Extractor\Speech()
            );
        }
    }
}
