<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Vote implements ExtractionInterface, IdentityInterface
{
    private string $id;
    private DOMElement $object;

    public function populate(DOMElement $object): self
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(): array
    {
        if (! $this->object->hasAttribute('málsnúmer')) {
            throw new Extractor\Exception('Missing [{málsnúmer}] value', $this->object);
        }

        if (! $this->object->hasAttribute('þingnúmer')) {
            throw new Extractor\Exception('Missing [{þingnúmer}] value', $this->object);
        }

        if (! $this->object->hasAttribute('atkvæðagreiðslunúmer')) {
            throw new Extractor\Exception('Missing [{atkvæðagreiðslunúmer}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('tími')) {
            throw new Extractor\Exception('Missing [{tími}] value', $this->object);
        }

        if (! $this->object->getElementsByTagName('tegund')) {
            throw new Extractor\Exception('Missing [{tegund}] value', $this->object);
        }

        $xpath = new \DOMXPath($this->object->ownerDocument);

        //DOCUMENT
        $documentNodeList = $xpath->query('//atkvæðagreiðsla/þingskjal');
        $document = $documentNodeList?->item(0)?->getAttribute('skjalsnúmer');

        //OUTCOME
        $outcomeNodeList = $xpath->query('//atkvæðagreiðsla/niðurstaða/niðurstaða');
        $outcome = $outcomeNodeList?->item(0)?->nodeValue;

        //METHOD
        $methodNodeList = $xpath->query('//atkvæðagreiðsla/niðurstaða/aðferð');
        $method = $methodNodeList?->item(0)?->nodeValue;

        //YES
        $yesNodeList = $xpath->query('//atkvæðagreiðsla/niðurstaða/já/fjöldi');
        $yes = $yesNodeList?->item(0)?->nodeValue;

        //NO
        $noNodeList = $xpath->query('//atkvæðagreiðsla/niðurstaða/nei/fjöldi');
        $no = $noNodeList?->item(0)?->nodeValue;

        //INACTION
        $inactionNodeList = $xpath->query('//atkvæðagreiðsla/niðurstaða/greiðirekkiatkvæði/fjöldi');
        $inaction = $inactionNodeList?->item(0)?->nodeValue;

        //COMMITTEE
        $committee = $this->object->getElementsByTagName('til')?->item(0)?->nodeValue;

        $this->setIdentity((int) $this->object->getAttribute('atkvæðagreiðslunúmer'));

        return [
            'issue_id' => (int) $this->object->getAttribute('málsnúmer'),
            'assembly_id' => (int) $this->object->getAttribute('þingnúmer'),
            'vote_id' => (int) $this->object->getAttribute('atkvæðagreiðslunúmer'),
            'document_id' => (int) $document,
            'type' => trim($this->object->getElementsByTagName('tegund')->item(0)->nodeValue),
            'date' => date('Y-m-d H:i:s', strtotime($this->object->getElementsByTagName('tími')->item(0)->nodeValue)),
            'outcome' => $outcome ? trim($outcome) : null,
            'method' => $method ? trim($method) : null,
            'yes' => $yes ? (int) $yes : 0,
            'no' => $no ? (int) $no : 0,
            'inaction' => $inaction ? (int) $inaction : 0,
            'committee_to' => $committee ? trim($committee) : null
        ];
    }

    public function setIdentity(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity(): string
    {
        return $this->id;
    }
}
