<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class Speech implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * @throws \App\Extractor\Exception
     */
    public function extract(DOMElement $object): array
    {
        if (! $object->hasAttribute('fundarnúmer')) {
            throw new Extractor\Exception('Missing [{fundarnúmer}] value', $object);
        }

        if (! $object->hasAttribute('þingnúmer')) {
            throw new Extractor\Exception('Missing [{þingnúmer}] value', $object);
        }

        if (! $object->hasAttribute('þingmaður')) {
            throw new Extractor\Exception('Missing [{þingmaður}] value', $object);
        }

        if (! $object->hasAttribute('þingmál')) {
            throw new Extractor\Exception('Missing [{þingmál}] value', $object);
        }

        $this->setIdentity($this->createIdentity($object));

        if ($object->getElementsByTagName('ræðahófst')->item(0)) {
            $from = $this->resolveDate($object->getElementsByTagName('ræðahófst')->item(0));
            $to = $this->resolveDate($object->getElementsByTagName('ræðulauk')->item(0));
        } elseif ($object->getElementsByTagName('ræðudagur')->item(0)) {
            $from = $this->resolveDate($object->getElementsByTagName('ræðudagur')->item(0));
            $to = $this->resolveDate($object->getElementsByTagName('ræðudagur')->item(0));
        }

        $plenaryId = (int) $object->getAttribute('fundarnúmer');
        $assemblyId = (int) $object->getAttribute('þingnúmer');
        $issueId = (int) $object->getAttribute('þingmál');
        $congressmanType = $this->resolveCongressmanType($object);
        $congressmanId = (int) $object->getAttribute('þingmaður');
        $iteration = $this->resolveIteration($object->getElementsByTagName('umræða')->item(0));
        $type = ($object->getElementsByTagName('tegundræðu')->item(0))
            ? $object->getElementsByTagName('tegundræðu')->item(0)->nodeValue
            : null;
        $text = ($object->getElementsByTagName('ræðutexti')->item(0))
            ? $object->getElementsByTagName('ræðutexti')
                ->item(0)->ownerDocument->saveXML($object->getElementsByTagName('ræðutexti')->item(0))
            : null;

        $issue = $object->getElementsByTagName('mál');
        $issueType = $issue->length && $issue->item(0)->hasAttribute('málsflokkur')
            ? $issue->item(0)->getAttribute('málsflokkur')
            : 'A';

        return [
            'id' => $this->getIdentity(),
            'from' => $from,
            'to' => $to,
            'plenary_id' => $plenaryId,
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'congressman_type' => $congressmanType,
            'congressman_id' => $congressmanId,
            'iteration' => $iteration,
            'type' => $type,
            'text' => $text,
            'category' => $issueType,
            'validated' => $object->hasAttribute('temporary') ? 'false' : 'true'
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
