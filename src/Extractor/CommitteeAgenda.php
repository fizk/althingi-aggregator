<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class CommitteeAgenda implements ExtractionInterface, IdentityInterface
{
    private string $id;

    /**
     * @todo extract <Gestir />
     *
     *  <dagskrárliður númer="1">
     *      <mál málsnúmer="1" löggjafarþing="145" málsflokkur="A">...</mál>
     *     <heiti>
     *         <![CDATA[ fjárlög 2016 ]]>
     *     </heiti>
     *     <Gestir/>
     *  </dagskrárliður>
     *
     *
     */
    public function extract(DOMElement $object): array
    {
        if (! $object->hasAttribute('númer')) {
            throw new Extractor\Exception('Missing [{númer}] value', $object);
        }

        $this->setIdentity((int) $object->getAttribute('númer'));

        $issueId = ($object->getElementsByTagName('mál')->item(0))
            ? (int) $object->getElementsByTagName('mál')->item(0)->getAttribute('málsnúmer')
            : null ;

        $title = ($object->getElementsByTagName('heiti')->item(0))
            ? trim($object->getElementsByTagName('heiti')->item(0)->nodeValue)
            : null ;

        return [
            'issue_id' => $issueId,
            'title' => $title
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
