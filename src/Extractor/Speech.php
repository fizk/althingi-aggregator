<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Speech implements ExtractionInterface, IdentityInterface
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

        if (! $this->object->hasAttribute('þingnúmer')) {
            throw new Extractor\Exception('Missing [{þingnúmer}] value', $this->object);
        }

        if (! $this->object->hasAttribute('þingmaður')) {
            throw new Extractor\Exception('Missing [{þingmaður}] value', $this->object);
        }

        if (! $this->object->hasAttribute('þingmál')) {
            throw new Extractor\Exception('Missing [{þingmál}] value', $this->object);
        }

        $this->setIdentity($this->createIdentity($this->object));

        if ($this->object->getElementsByTagName('ræðahófst')->item(0)) {
            $from = $this->resolveDate($this->object->getElementsByTagName('ræðahófst')->item(0));
            $to = $this->resolveDate($this->object->getElementsByTagName('ræðulauk')->item(0));
        } elseif ($this->object->getElementsByTagName('ræðudagur')->item(0)) {
            $from = $this->resolveDate($this->object->getElementsByTagName('ræðudagur')->item(0));
            $to = $this->resolveDate($this->object->getElementsByTagName('ræðudagur')->item(0));
        }

        $plenaryValue = $this->object->getAttribute('fundarnúmer');
        $plenaryId = $plenaryValue === null
            ? 0
            : (((int) $plenaryValue) > 0 ? $plenaryValue : 0);
        $assemblyId = (int) $this->object->getAttribute('þingnúmer');
        $issueId = (int) $this->object->getAttribute('þingmál');
        $congressmanType = $this->resolveCongressmanType($this->object);
        $congressmanId = (int) $this->object->getAttribute('þingmaður');
        $iteration = $this->resolveIteration($this->object->getElementsByTagName('umræða')->item(0));
        $type = $this->object->getElementsByTagName('tegundræðu')?->item(0)?->nodeValue;
        $text = $this->object->getElementsByTagName('ræðutexti')
                ?->item(0)?->ownerDocument?->saveXML($this->object->getElementsByTagName('ræðutexti')->item(0));

        $issue = $this->object->getElementsByTagName('mál');
        $issueType = $issue?->item(0)?->hasAttribute('málsflokkur')
            ? $issue->item(0)->getAttribute('málsflokkur')
            : 'A';

        return [
            'id' => $this->getIdentity(),
            'from' => $from,
            'to' => $to,
            'plenary_id' => (int) $plenaryId,
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'congressman_type' => $congressmanType,
            'congressman_id' => $congressmanId,
            'iteration' => $iteration,
            'type' => $type,
            'text' => $text,
            'category' => $issueType,
            'validated' => $this->object->hasAttribute('temporary') ? 'false' : 'true'
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

    private function resolveCongressmanType(DOMElement $object): ?string
    {
        if ($object->getElementsByTagName('ráðherra')->item(0)) {
            return $object->getElementsByTagName('ráðherra')->item(0)->nodeValue;
        }

        if ($object->getElementsByTagName('forsetiAlþingis')->item(0)) {
            return 'forseti alþingis';
        }

        return null;
    }

    private function resolveDate(DOMElement $element = null): ?string
    {
        if (! $element) {
            return null;
        }

        return date('Y-m-d H:i:s', strtotime($element->nodeValue));
    }

    private function createIdentity(DOMElement $element): string
    {
        if ($element->getElementsByTagName('ræðahófst')->item(0)) {
            return str_replace(
                ['-', ':'],
                ['', ''],
                $element->getElementsByTagName('ræðahófst')->item(0)->nodeValue
            );
        } elseif ($element->getElementsByTagName('ræðudagur')->item(0)) {
            return md5($element->ownerDocument->saveXML());  //TODO this is not good
        }

        return md5(time()); //TODO this is not good
    }

    private function resolveIteration(DOMElement $element = null): ?int
    {
        if (! $element) {
            return null;
        }

        if (is_numeric($element->nodeValue)) {
            return (int) $element->nodeValue;
        }

        return null;
    }
}
