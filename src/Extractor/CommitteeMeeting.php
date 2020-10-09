<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class CommitteeMeeting implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * @throws Extractor\Exception
     */
    public function extract(DOMElement $object): array
    {
        if (! $object->hasAttribute('númer')) {
            throw new Extractor\Exception('Missing [{númer}] value', $object);
        }

        $this->setIdentity((int) $object->getAttribute('númer'));

        $from = ($object->getElementsByTagName('fundursettur')->item(0) &&
            ! empty($object->getElementsByTagName('fundursettur')->item(0)->nodeValue))
            ? date('Y-m-d H:i:s', strtotime($object->getElementsByTagName('fundursettur')->item(0)->nodeValue))
            : null ;

        $to = ($object->getElementsByTagName('fuslit')->item(0) &&
            ! empty($object->getElementsByTagName('fuslit')->item(0)->nodeValue))
            ? date('Y-m-d H:i:s', strtotime($object->getElementsByTagName('fuslit')->item(0)->nodeValue))
            : null ;

        $description = ($object->getElementsByTagName('fundargerð')->item(0) &&
            $object->getElementsByTagName('fundargerð')->item(0)->getElementsByTagName('texti')->item(0))
            ? trim($object->getElementsByTagName('fundargerð')
                ->item(0)->getElementsByTagName('texti')->item(0)->nodeValue)
            : null ;

        return [
            'from' => $from,
            'to' => $to,
            'description' => $description
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